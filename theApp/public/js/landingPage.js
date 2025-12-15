// =======================
// Global Variables
// =======================
let bookmarks = [];
let currentArtworkId = null;

// =======================
// Video Background Control
// =======================
document.addEventListener('DOMContentLoaded', function () {
    const video = document.getElementById('bgVideo');

    // Play video when page loads
    video.play().catch(e => {
        console.log("Video autoplay failed:", e);
        // Fallback: show static background if video fails
        video.style.display = 'none';
        document.querySelector('.video-container').style.background = 'linear-gradient(135deg, #e53e3e 0%, #9b1c1c 100%)';
    });

    // Pause video when not visible to save resources
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            video.pause();
        } else {
            video.play();
        }
    });

    // =======================
    // Upload Image Handling
    // =======================
    const mainImageContainer = document.getElementById('mainImageContainer');
    const mainImageInput = document.getElementById('mainImageInput');
    const mainImagePreview = document.getElementById('mainImagePreview');
    const mainImagePlaceholder = document.getElementById('mainImagePlaceholder');
    const additionalImagesInput = document.getElementById('additionalImagesInput');
    const additionalImagesPreview = document.getElementById('additionalImagesPreview');

    // Click to upload main image
    mainImageContainer.addEventListener('click', function () {
        mainImageInput.click();
    });

    // Preview main image
    mainImageInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                mainImagePreview.querySelector('img').src = e.target.result;
                mainImagePreview.classList.remove('hidden');
                mainImagePlaceholder.classList.add('hidden');
            }

            reader.readAsDataURL(this.files[0]);
        }
    });

    // Preview additional images
    additionalImagesInput.addEventListener('change', function () {
        additionalImagesPreview.innerHTML = '';

        if (this.files) {
            // Limit to 5 files
            const files = Array.from(this.files).slice(0, 5);

            files.forEach((file, index) => {
                const reader = new FileReader();

                reader.onload = function (e) {
                    const imageContainer = document.createElement('div');
                    imageContainer.className = 'relative group';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-20 object-cover rounded-lg';

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity';
                    removeBtn.innerHTML = '<i class="fas fa-times text-xs"></i>';
                    removeBtn.onclick = function () {
                        imageContainer.remove();
                    };

                    imageContainer.appendChild(img);
                    imageContainer.appendChild(removeBtn);
                    additionalImagesPreview.appendChild(imageContainer);
                }

                reader.readAsDataURL(file);
            });
        }
    });

    // Drag and drop for main image
    mainImageContainer.addEventListener('dragover', function (e) {
        e.preventDefault();
        this.classList.add('border-red-500', 'bg-red-50');
    });

    mainImageContainer.addEventListener('dragleave', function (e) {
        e.preventDefault();
        this.classList.remove('border-red-500', 'bg-red-50');
    });

    mainImageContainer.addEventListener('drop', function (e) {
        e.preventDefault();
        this.classList.remove('border-red-500', 'bg-red-50');

        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            mainImageInput.files = e.dataTransfer.files;

            const event = new Event('change', { bubbles: true });
            mainImageInput.dispatchEvent(event);
        }
    });
});

// =======================
// Fetch Artwork View (Backend)
// =======================
async function fetchArtworkView(artworkId) {
    try {
        const response = await fetch(`/artworks/${artworkId}/view`);
        if (!response.ok) throw new Error('Gagal ambil data');
        return await response.json();
    } catch (err) {
        console.error(err);
        alert('Terjadi kesalahan saat memuat karya.');
    }
}

// =======================
// Comments Functions - PERUBAHAN BARU
// =======================

// Add new comment
// Add new comment
async function addComment(event) {
    event.preventDefault();

    const form = event.target;
    // Get comment content (using name="content" or id="commentInput")
    const contentInput = document.getElementById('commentInput');
    const content = contentInput.value.trim();
    const artworkId = document.getElementById('commentArtworkId').value;

    if (!content) return;

    // Show loading state if desired (optional)
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    submitBtn.disabled = true;

    try {
        const formData = new FormData();
        formData.append('artwork_id', artworkId);
        formData.append('content', content);

        const response = await fetch('/comments', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });

        if (response.ok) {
            const newComment = await response.json();

            // Add to DOM
            const container = document.getElementById('commentsContainer');
            const noCommentsMessage = document.getElementById('noCommentsMessage');

            // Remove no comments message if it exists
            if (noCommentsMessage) {
                noCommentsMessage.remove();
            }

            // Create and add the new comment element
            const commentElement = createCommentElement(newComment);
            container.insertBefore(commentElement, container.firstChild);

            // Update comment count
            const countElement = document.getElementById('popupComments');
            if (countElement) {
                countElement.textContent = parseInt(countElement.textContent || '0') + 1;
            }

            // Clear the form
            form.reset();

            // Show success message
            showNotification('Komentar berhasil ditambahkan!', 'success');
        } else {
            console.error('Failed to submit comment');
            const errorData = await response.json();
            showNotification(errorData.message || 'Gagal mengirim komentar', 'error');
        }
    } catch (error) {
        console.error('Error submitting comment:', error);
        showNotification('Terjadi kesalahan koneksi', 'error');
    } finally {
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    }
}
function renderComments(comments) {
    const container = document.getElementById('commentsContainer');

    if (!comments || comments.length === 0) {
        container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-comment-slash text-2xl mb-2"></i>
                        <p>Belum ada komentar</p>
                    </div>
                `;
        return;
    }

    container.innerHTML = comments.map(comment => {
        const isOwner = window.currentUserId && String(comment.user_id) === String(window.currentUserId);
        const deleteBtn = isOwner ?
            `<button onclick="deleteComment(event, ${comment.id})" class="text-red-500 hover:text-red-700 text-sm ml-auto" title="Hapus komentar">
                <i class="fas fa-trash"></i>
            </button>` : '';

        return `
            <div class="comment-item border-b border-gray-200 py-4" id="comment-${comment.id}">
                <div class="flex items-start gap-3">
                    <img src="${comment.user_avatar || '/images/default-avatar.png'}" 
                        alt="${comment.user_name}" 
                        class="w-10 h-10 rounded-full object-cover"
                        onerror="this.src='/images/default-avatar.png'">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1 justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold">${comment.user_name}</span>
                                <span class="text-sm text-gray-500">${formatDate(comment.created_at)}</span>
                            </div>
                            ${deleteBtn}
                        </div>
                        <p class="text-gray-700">${escapeHtml(comment.content)}</p>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Helper function untuk format tanggal
function formatDate(dateString) {
    if (!dateString) return '';

    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;

    // Jika kurang dari 1 menit
    if (diff < 60000) return 'Baru saja';

    // Jika kurang dari 1 jam
    if (diff < 3600000) {
        const minutes = Math.floor(diff / 60000);
        return `${minutes} menit yang lalu`;
    }

    // Jika kurang dari 1 hari
    if (diff < 86400000) {
        const hours = Math.floor(diff / 3600000);
        return `${hours} jam yang lalu`;
    }

    // Jika kurang dari 7 hari
    if (diff < 604800000) {
        const days = Math.floor(diff / 86400000);
        return `${days} hari yang lalu`;
    }

    // Tampilkan tanggal lengkap
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Helper function untuk escape HTML (keamanan)
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
async function loadComments(artworkId) {
    console.log('Loading comments for artwork ID:', artworkId);

    try {
        const response = await fetch(`/comments/${artworkId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);

        if (!response.ok) {
            const errorData = await response.json();
            console.error('Error response:', errorData);
            throw new Error(errorData.error || 'Gagal memuat komentar');
        }

        const comments = await response.json();
        console.log('Comments data:', comments);

        renderComments(comments);
    } catch (error) {
        console.error('Error loading comments:', error);
        // Tampilkan pesan error
        const container = document.getElementById('commentsContainer');
        container.innerHTML = `
                    <div class="text-center py-8 text-red-500">
                        <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                        <p>Gagal memuat komentar. Silakan coba lagi.</p>
                        <p class="text-sm mt-2">Error: ${error.message}</p>
                    </div>
                `;
    }
}

// Create comment element
function createCommentElement(comment) {
    const commentDiv = document.createElement('div');
    commentDiv.className = 'comment-item';

    // Format date
    const date = new Date(comment.created_at);
    const formattedDate = date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    const isOwner = window.currentUserId && String(comment.user_id) === String(window.currentUserId);
    const deleteBtn = isOwner ?
        `<button onclick="deleteComment(event, ${comment.id})" class="text-red-500 hover:text-red-700 text-sm ml-auto" title="Hapus komentar">
            <i class="fas fa-trash"></i>
        </button>` : '';

    commentDiv.id = `comment-${comment.id}`;
    commentDiv.innerHTML = `
                <div class="comment-header flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <img src="${comment.user.avatar}" 
                             alt="${comment.user.name}" class="comment-avatar">
                        <div class="comment-meta">
                            <div class="comment-author">${comment.user.name}</div>
                            <div class="comment-time">${formattedDate}</div>
                        </div>
                    </div>
                    ${deleteBtn}
                </div>
                <div class="comment-body mt-2">
                    <p class="comment-text">${comment.content}</p>
                </div>
            `;

    return commentDiv;
}

// Delete comment
async function deleteComment(event, commentId) {
    if (event) event.preventDefault();

    if (!confirm('Apakah Anda yakin ingin menghapus komentar ini?')) return;

    try {
        const response = await fetch(`/comments/${commentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            // Remove from DOM
            const commentElement = document.getElementById(`comment-${commentId}`);
            if (commentElement) {
                commentElement.remove();

                // Update count
                const countElement = document.getElementById('popupComments');
                const currentCount = parseInt(countElement.textContent);
                countElement.textContent = Math.max(0, currentCount - 1);

                showNotification('Komentar berhasil dihapus', 'success');

                // Show empty state if needed
                const container = document.getElementById('commentsContainer');
                if (container.children.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-comment-slash text-2xl mb-2"></i>
                            <p>Belum ada komentar</p>
                        </div>
                    `;
                }
            }
        } else {
            const data = await response.json();
            showNotification(data.error || 'Gagal menghapus komentar', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat menghapus komentar', 'error');
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        } text-white`;
    notification.textContent = message;

    document.body.appendChild(notification);

    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}


// =======================
// Artwork Popup
// =======================
async function openArtworkPopup(artworkId) {
    currentArtworkId = artworkId;

    try {
        const data = await fetchArtworkView(artworkId);

        if (!data) throw new Error("Data not found");

        // Set popup content
        document.getElementById('popupTitle').textContent = data.title;
        document.getElementById('popupArtist').textContent = data.user.name;
        document.getElementById('popupTime').textContent = data.created_at;
        document.getElementById('popupArtistAvatar').src = data.user.avatar;
        document.getElementById('popupDescription').textContent = data.description || 'Tidak ada deskripsi';

        // Set main image
        const mainImage = document.getElementById('popupMainImage');
        mainImage.src = data.main_image;

        // Set thumbnails dynamically
        const thumbnailGrid = document.getElementById('thumbnailGrid');
        thumbnailGrid.innerHTML = '';

        // Create thumbnail for main image
        const mainThumbnail = createThumbnail(data.main_image, 0);
        mainThumbnail.classList.add('active');
        thumbnailGrid.appendChild(mainThumbnail);

        // Create thumbnails for additional images
        if (data.additional_images && data.additional_images.length > 0) {
            data.additional_images.forEach((imageSrc, index) => {
                const thumbnail = createThumbnail(imageSrc, index + 1);
                thumbnailGrid.appendChild(thumbnail);
            });
        }

        // Set tags
        const tagsContainer = document.getElementById('popupTags');
        tagsContainer.innerHTML = '';
        if (data.tags && data.tags.length > 0) {
            document.getElementById('popupTagsContainer').style.display = 'block';
            data.tags.forEach(tag => {
                const tagElement = document.createElement('span');
                tagElement.className = 'tag';
                tagElement.textContent = `#${tag}`;
                tagsContainer.appendChild(tagElement);
            });
        } else {
            document.getElementById('popupTagsContainer').style.display = 'none';
        }

        // Set stats
        document.getElementById('popupComments').textContent = data.comments_count;
        document.getElementById('popupViews').textContent = data.views;

        // Set bookmark state
        const bookmarkBtn = document.getElementById('popupBookmarkBtn');
        const bookmarkIcon = bookmarkBtn.querySelector('i');
        if (bookmarks.includes(artworkId)) {
            bookmarkIcon.classList.remove('far');
            bookmarkIcon.classList.add('fas');
            bookmarkBtn.classList.add('bookmarked');
        } else {
            bookmarkIcon.classList.remove('fas');
            bookmarkIcon.classList.add('far');
            bookmarkBtn.classList.remove('bookmarked');
        }

        // Set artwork_id in comment form
        const commentArtworkIdInput = document.getElementById('commentArtworkId');
        if (commentArtworkIdInput) {
            commentArtworkIdInput.value = artworkId;
        }

        // Show popup
        document.getElementById('artworkPopup').classList.add('active');
        document.body.style.overflow = 'hidden';

        // Load comments
        loadComments(artworkId);
    } catch (e) {
        console.error(e);
        alert("Gagal memuat karya");
    }
}

// Function to create thumbnail
function createThumbnail(src, index) {
    const thumbnail = document.createElement('img');
    thumbnail.src = src;
    thumbnail.alt = `Thumbnail ${index}`;
    thumbnail.className = 'thumbnail';
    thumbnail.dataset.index = index;

    thumbnail.onclick = function () {
        changeMainImage(this.src);
        // Update active state
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });
        this.classList.add('active');
    };

    return thumbnail;
}

function closeArtworkPopup() {
    document.getElementById('artworkPopup').classList.remove('active');
    document.body.style.overflow = 'auto';
    currentArtworkId = null;
}

// =======================
// Change Main Image in Popup
// =======================
function changeMainImage(src) {
    const mainImage = document.getElementById('popupMainImage');

    // Add fade effect
    mainImage.style.opacity = '0.5';

    // Change image source
    mainImage.src = src;

    // Restore opacity after image loads
    mainImage.onload = function () {
        mainImage.style.opacity = '1';
    };

    // Fallback if image fails to load
    mainImage.onerror = function () {
        mainImage.style.opacity = '1';
        mainImage.src = 'https://via.placeholder.com/600x400?text=Image+Not+Available';
    };
}

// =======================
// Bookmark
// =======================
async function toggleBookmark(event, artworkId) {
    if (event) event.stopPropagation();

    // Determine context (card or popup)
    let icon, button;
    const popupBtn = document.getElementById('popupBookmarkBtn');

    // Check if called from popup
    if (event && event.currentTarget && event.currentTarget.id === 'popupBookmarkBtn') {
        button = popupBtn;
        icon = button.querySelector('i');
    } else {
        // Called from card
        const card = document.querySelector(`[data-artwork-id="${artworkId}"]`);
        if (card) {
            button = card.querySelector(".action-button");
            icon = button.querySelector(".action-button i");
        }
    }

    if (!icon || !button) return;

    // Check Auth
    if (!checkAuth()) {
        openAuthCheckModal();
        return;
    }

    const isBookmarked = icon.classList.contains("fas");


    try {
        if (isBookmarked) {
            // Hapus bookmark
            const res = await fetch(`/bookmarks/${artworkId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json",
                },
            });

            if (res.ok) {
                // Update UI for BOTH card and popup
                updateBookmarkUI(artworkId, isBookmarked ? 'remove' : 'add');

                // Update global state
                if (isBookmarked) {
                    bookmarks = bookmarks.filter(id => id != artworkId);
                    if (!document.getElementById('bookmarksSection').classList.contains('section-hidden')) {
                        renderBookmarks();
                    }
                } else {
                    if (!bookmarks.includes(Number(artworkId))) {
                        bookmarks.push(Number(artworkId));
                    }
                }
            } else {
                console.error(isBookmarked ? "Gagal hapus bookmark" : "Gagal tambah bookmark");
            }
        } else {
            // Tambah bookmark
            const res = await fetch(`/bookmarks`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                },
                body: JSON.stringify({ artwork_id: artworkId }),
            });

            if (res.ok) {
                // Update UI for BOTH card and popup
                updateBookmarkUI(artworkId, 'add');

                // Update global state
                if (!bookmarks.includes(Number(artworkId))) {
                    bookmarks.push(Number(artworkId));
                }
            } else {
                console.error("Gagal tambah bookmark:", await res.json());
            }
        }
    } catch (err) {
        console.error("Error toggle bookmark:", err);
        alert("Terjadi kesalahan saat menyimpan bookmark.");
    }
}

// Helper to update UI
function updateBookmarkUI(artworkId, action) {
    // 1. Update Popup Button
    const popupBtn = document.getElementById('popupBookmarkBtn');
    if (popupBtn && typeof currentArtworkId !== 'undefined' && currentArtworkId == artworkId) {
        const icon = popupBtn.querySelector('i');
        if (action === 'add') {
            icon.classList.remove('far');
            icon.classList.add('fas');
            popupBtn.classList.add('bookmarked');
        } else {
            icon.classList.remove('fas');
            icon.classList.add('far');
            popupBtn.classList.remove('bookmarked');
        }
    }

    // 2. Update Card Button
    const card = document.querySelector(`[data-artwork-id="${artworkId}"]`);
    if (card) {
        const icon = card.querySelector(".action-button i");
        const button = card.querySelector(".action-button");
        if (action === 'add') {
            icon.classList.remove('far');
            icon.classList.add('fas');
            button.classList.add('bookmarked');
        } else {
            icon.classList.remove('fas');
            icon.classList.add('far');
            button.classList.remove('bookmarked');
        }
    }
}

// Inisialisasi: tandai bookmark yang sudah ada di DB saat halaman pertama load
document.addEventListener("DOMContentLoaded", async () => {
    try {
        const res = await fetch("/bookmarks", {
            headers: { "Accept": "application/json" }
        });
        if (!res.ok) return;
        const bookmarkedIds = await res.json();
        bookmarks = bookmarkedIds; // Update global variable from server

        bookmarkedIds.forEach(id => {
            const card = document.querySelector(`[data-artwork-id="${id}"]`);
            if (card) {
                const icon = card.querySelector(".action-button i");
                const button = card.querySelector(".action-button");
                icon.classList.remove("far");
                icon.classList.add("fas");
                button.classList.add("bookmarked");
            }
        });

        // Auto render bookmarks section
        renderBookmarks();
    } catch (err) {
        console.error("Gagal memuat data bookmark:", err);
    }
});

async function clearAllBookmarks() {
    if (!confirm("Yakin hapus semua bookmark?")) return;

    try {
        await fetch(`/bookmarks/clear-all`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json",
            },
        });

        document.querySelectorAll(".action-button i.fa-bookmark").forEach(icon => {
            icon.classList.remove("fas");
            icon.classList.add("far");
            icon.closest(".action-button").classList.remove("bookmarked");
        });

        bookmarks = [];
        renderBookmarks();

    } catch (err) {
        console.error("Error hapus semua bookmark:", err);
        alert("Gagal hapus semua bookmark");
    }
}

// =======================
// Section Navigation
// =======================
function showSection(section) {
    document.getElementById('gallerySection').classList.add('section-hidden');
    document.getElementById('bookmarksSection').classList.add('section-hidden');

    if (section === 'gallery') {
        document.getElementById('gallerySection').classList.remove('section-hidden');

        // Update styling
        document.getElementById('btnGallery').classList.add('text-gray-800', 'border-b-2', 'border-red-500');
        document.getElementById('btnGallery').classList.remove('text-gray-400');

        document.getElementById('btnBookmarks').classList.add('text-gray-400');
        document.getElementById('btnBookmarks').classList.remove('text-gray-800', 'border-b-2', 'border-red-500');

    } else if (section === 'bookmarks') {
        document.getElementById('bookmarksSection').classList.remove('section-hidden');
        renderBookmarks();

        // Update styling
        document.getElementById('btnBookmarks').classList.add('text-gray-800', 'border-b-2', 'border-red-500');
        document.getElementById('btnBookmarks').classList.remove('text-gray-400');

        document.getElementById('btnGallery').classList.add('text-gray-400');
        document.getElementById('btnGallery').classList.remove('text-gray-800', 'border-b-2', 'border-red-500');
    }
}

function renderBookmarks() {
    const container = document.getElementById('bookmarksContainer');
    const emptyState = document.getElementById('emptyBookmarks');
    container.innerHTML = '';

    if (bookmarks.length === 0) {
        emptyState.classList.remove('hidden');
    } else {
        emptyState.classList.add('hidden');
        bookmarks.forEach(id => {
            const card = document.querySelector(`[data-artwork-id="${id}"]`);
            if (card) container.appendChild(card.cloneNode(true));
        });
    }
}

// =======================
// Modals: Upload, Profile, Report, Comments
// =======================
function openUploadModal() {
    // Check Auth
    if (!checkAuth()) {
        openAuthCheckModal();
        return;
    }

    const modal = document.getElementById('uploadModal');
    const modalContent = document.getElementById('uploadModalContent');

    modal.classList.remove('hidden');

    // Add animation
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeUploadModal() {
    const modal = document.getElementById('uploadModal');
    const modalContent = document.getElementById('uploadModalContent');

    // Add animation
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        // Reset form
        document.querySelector('#uploadModal form').reset();
        document.getElementById('mainImagePreview').classList.add('hidden');
        document.getElementById('mainImagePlaceholder').classList.remove('hidden');
        document.getElementById('additionalImagesPreview').innerHTML = '';
    }, 300);
}

function openReportModal(event, artworkId) {
    if (event) event.stopPropagation();

    // Check Auth
    if (!checkAuth()) {
        openAuthCheckModal();
        return;
    }

    if (!artworkId) return;

    // Hide profile menu if open
    const profileMenu = document.getElementById('profileMenu');
    if (profileMenu) profileMenu.classList.add('hidden');

    document.getElementById('reportArtworkId').value = artworkId;

    // Animation logic
    const modal = document.getElementById('reportModal');
    const modalContent = document.getElementById('reportModalContent');

    modal.classList.remove('hidden');
    // Force reflow
    void modal.offsetWidth;

    modalContent.classList.remove('scale-95', 'opacity-0');
    modalContent.classList.add('scale-100', 'opacity-100');
}

function closeReportModal() {
    const modal = document.getElementById('reportModal');
    const modalContent = document.getElementById('reportModalContent');

    // Animation logic
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        document.getElementById('reportForm').reset();
    }, 300);
}

async function handleReport(event) {
    event.preventDefault();

    const form = event.target;
    // Get reason from form
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('/reports', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            alert('Laporan berhasil dikirim!');
            closeReportModal();
        } else {
            const result = await response.json();
            alert(result.message || 'Gagal mengirim laporan');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengirim laporan.');
    }
}

function openCommentsModal(event) {
    if (event) event.stopPropagation();
    document.getElementById('commentsModal').classList.remove('hidden');
}

function closeCommentsModal() {
    document.getElementById('commentsModal').classList.add('hidden');
}

// =======================
// Profile Menu Toggle
// =======================
function toggleProfileMenu() {
    document.getElementById('profileMenu').classList.toggle('hidden');
}

// =======================
// Close Modals on Click Outside
// =======================
window.onclick = function (event) {
    if (event.target.classList.contains('modal-backdrop')) {
        event.target.classList.add('hidden');
    }
    if (event.target.id === 'artworkPopup') {
        closeArtworkPopup();
    }
}

// =======================
// Close Popup with Escape Key
// =======================
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeArtworkPopup();
    }
});

// =======================
// Auth Check Helper
// =======================
function checkAuth() {
    return window.currentUserId && window.currentUserId !== "";
}

function openAuthCheckModal() {
    const modal = document.getElementById('authCheckModal');
    const modalContent = document.getElementById('authCheckModalContent');

    modal.classList.remove('hidden');
    // Force reflow
    void modal.offsetWidth;

    modalContent.classList.remove('scale-95', 'opacity-0');
    modalContent.classList.add('scale-100', 'opacity-100');
}

function closeAuthCheckModal() {
    const modal = document.getElementById('authCheckModal');
    const modalContent = document.getElementById('authCheckModalContent');

    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}