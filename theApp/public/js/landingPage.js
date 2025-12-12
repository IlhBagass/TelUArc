        // =======================
        // Global Variables
        // =======================
        let bookmarks = JSON.parse(localStorage.getItem('bookmarks')) || [];
        let currentArtworkId = null;

        // =======================
        // Video Background Control
        // =======================
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('bgVideo');
            
            // Play video when page loads
            video.play().catch(e => {
                console.log("Video autoplay failed:", e);
                // Fallback: show static background if video fails
                video.style.display = 'none';
                document.querySelector('.video-container').style.background = 'linear-gradient(135deg, #e53e3e 0%, #9b1c1c 100%)';
            });
            
            // Pause video when not visible to save resources
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    video.pause();
                } else {
                    video.play();
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

        // Fetch comments from backend
        async function fetchComments(artworkId) {
            try {
                const response = await fetch(`/artworks/${artworkId}/comments`);
                if (!response.ok) throw new Error('Gagal mengambil komentar');
                return await response.json();
            } catch (err) {
                console.error(err);
                return [];
            }
        }

        // Render comments in popup
        function renderComments(comments) {
            const container = document.getElementById('commentsContainer');
            const noCommentsMessage = document.getElementById('noCommentsMessage');
            
            // Clear existing comments
            container.innerHTML = '';
            
            if (comments.length === 0) {
                container.appendChild(noCommentsMessage);
                noCommentsMessage.classList.remove('hidden');
            } else {
                noCommentsMessage.classList.add('hidden');
                
                comments.forEach(comment => {
                    const commentElement = createCommentElement(comment);
                    container.appendChild(commentElement);
                });
            }
        }

        // Create comment element
        function createCommentElement(comment) {
            const commentDiv = document.createElement('div');
            commentDiv.className = 'flex space-x-3';
            
            // User avatar
            const avatar = document.createElement('img');
            avatar.src = comment.user.avatar || `https://picsum.photos/seed/${comment.user.id}/40/40`;
            avatar.alt = 'User';
            avatar.className = 'w-10 h-10 rounded-full';
            
            // Comment content
            const contentDiv = document.createElement('div');
            contentDiv.className = 'flex-1';
            
            // Comment bubble
            const bubbleDiv = document.createElement('div');
            bubbleDiv.className = 'bg-gray-50 rounded-xl p-4';
            
            // Username
            const username = document.createElement('p');
            username.className = 'font-medium';
            username.textContent = comment.user.name;
            
            // Comment text
            const text = document.createElement('p');
            text.className = 'mt-1';
            text.textContent = comment.content;
            
            bubbleDiv.appendChild(username);
            bubbleDiv.appendChild(text);
            
            // Comment meta
            const metaDiv = document.createElement('div');
            metaDiv.className = 'flex items-center space-x-4 mt-2 text-sm text-gray-500';
            
            // Time
            const time = document.createElement('span');
            time.textContent = comment.created_at;
            
            // Like button
            const likeBtn = document.createElement('button');
            likeBtn.className = 'hover:text-red-600';
            likeBtn.innerHTML = `<i class="${comment.liked ? 'fas' : 'far'} fa-heart"></i> ${comment.likes_count}`;
            likeBtn.onclick = () => toggleCommentLike(comment.id);
            
            metaDiv.appendChild(time);
            metaDiv.appendChild(likeBtn);
            
            contentDiv.appendChild(bubbleDiv);
            contentDiv.appendChild(metaDiv);
            
            commentDiv.appendChild(avatar);
            commentDiv.appendChild(contentDiv);
            
            return commentDiv;
        }

        // Add new comment
        async function addComment(event) {
            event.preventDefault();
            
            const commentInput = document.getElementById('commentInput');
            const content = commentInput.value.trim();
            
            if (!content) return;
            
            try {
                const response = await fetch(`/artworks/${currentArtworkId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ content })
                });
                
                if (!response.ok) throw new Error('Gagal menambah komentar');
                
                const newComment = await response.json();
                
                // Add comment to UI
                const container = document.getElementById('commentsContainer');
                const noCommentsMessage = document.getElementById('noCommentsMessage');
                
                if (!noCommentsMessage.classList.contains('hidden')) {
                    noCommentsMessage.classList.add('hidden');
                }
                
                const commentElement = createCommentElement(newComment);
                container.insertBefore(commentElement, container.firstChild);
                
                // Update comment count
                const countElement = document.getElementById('popupComments');
                countElement.textContent = parseInt(countElement.textContent) + 1;
                
                // Clear input
                commentInput.value = '';
                
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat menambah komentar.');
            }
        }

        // Toggle comment like
        async function toggleCommentLike(commentId) {
            try {
                const response = await fetch(`/comments/${commentId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) throw new Error('Gagal menyukai komentar');
                
                const data = await response.json();
                
                // Update UI
                const likeBtn = event.currentTarget;
                likeBtn.innerHTML = `<i class="${data.liked ? 'fas' : 'far'} fa-heart"></i> ${data.likes_count}`;
                
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat menyukai komentar.');
            }
        }

        // =======================
        // Artwork Popup - PERUBAHAN UTAMA
        // =======================
        async function openArtworkPopup(artworkId) {
            currentArtworkId = artworkId;

            const data = await fetchArtworkView(artworkId);
            if (!data) return;

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
            
            // Load comments - PERUBAHAN BARU
            const comments = await fetchComments(artworkId);
            renderComments(comments);

            // Show popup
            document.getElementById('artworkPopup').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // Function to create thumbnail
        function createThumbnail(src, index) {
            const thumbnail = document.createElement('img');
            thumbnail.src = src;
            thumbnail.alt = `Thumbnail ${index}`;
            thumbnail.className = 'thumbnail';
            thumbnail.dataset.index = index;
            
            thumbnail.onclick = function() {
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
            mainImage.onload = function() {
                mainImage.style.opacity = '1';
            };
            
            // Fallback if image fails to load
            mainImage.onerror = function() {
                mainImage.style.opacity = '1';
                mainImage.src = 'https://via.placeholder.com/600x400?text=Image+Not+Available';
            };
        }

        // =======================
        // Bookmark
        // =======================
        async function toggleBookmark(event, artworkId) {
            event.stopPropagation();

            const card = document.querySelector(`[data-artwork-id="${artworkId}"]`);
            const icon = card.querySelector(".action-button i");
            const button = card.querySelector(".action-button");
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
                        icon.classList.remove("fas");
                        icon.classList.add("far");
                        button.classList.remove("bookmarked");
                    } else {
                        console.error("Gagal hapus bookmark:", await res.json());
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
                        icon.classList.remove("far");
                        icon.classList.add("fas");
                        button.classList.add("bookmarked");
                    } else {
                        console.error("Gagal tambah bookmark:", await res.json());
                    }
                }
            } catch (err) {
                console.error("Error toggle bookmark:", err);
                alert("Terjadi kesalahan saat menyimpan bookmark.");
            }
        }

        // Inisialisasi: tandai bookmark yang sudah ada di DB saat halaman pertama load
        document.addEventListener("DOMContentLoaded", async () => {
            try {
                const res = await fetch("/bookmarks", {
                    headers: { "Accept": "application/json" }
                });
                if (!res.ok) return;
                const bookmarkedIds = await res.json(); // hasil array [1,2,3,...]

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
            } else if (section === 'bookmarks') {
                document.getElementById('bookmarksSection').classList.remove('section-hidden');
                renderBookmarks();
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

        // Handle main image upload
        document.addEventListener('DOMContentLoaded', function() {
            const mainImageContainer = document.getElementById('mainImageContainer');
            const mainImageInput = document.getElementById('mainImageInput');
            const mainImagePreview = document.getElementById('mainImagePreview');
            const mainImagePlaceholder = document.getElementById('mainImagePlaceholder');
            const additionalImagesInput = document.getElementById('additionalImagesInput');
            const additionalImagesPreview = document.getElementById('additionalImagesPreview');
            
            // Click to upload main image
            mainImageContainer.addEventListener('click', function() {
                mainImageInput.click();
            });
            
            // Preview main image
            mainImageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        mainImagePreview.querySelector('img').src = e.target.result;
                        mainImagePreview.classList.remove('hidden');
                        mainImagePlaceholder.classList.add('hidden');
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
            
            // Preview additional images
            additionalImagesInput.addEventListener('change', function() {
                additionalImagesPreview.innerHTML = '';
                
                if (this.files) {
                    // Limit to 5 files
                    const files = Array.from(this.files).slice(0, 5);
                    
                    files.forEach((file, index) => {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const imageContainer = document.createElement('div');
                            imageContainer.className = 'relative group';
                            
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'w-full h-20 object-cover rounded-lg';
                            
                            const removeBtn = document.createElement('button');
                            removeBtn.type = 'button';
                            removeBtn.className = 'absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity';
                            removeBtn.innerHTML = '<i class="fas fa-times text-xs"></i>';
                            removeBtn.onclick = function() {
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
            mainImageContainer.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('border-red-500', 'bg-red-50');
            });
            
            mainImageContainer.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('border-red-500', 'bg-red-50');
            });
            
            mainImageContainer.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('border-red-500', 'bg-red-50');
                
                if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                    mainImageInput.files = e.dataTransfer.files;
                    
                    const event = new Event('change', { bubbles: true });
                    mainImageInput.dispatchEvent(event);
                }
            });
        });

        function openReportModal() { 
            document.getElementById('reportModal').classList.remove('hidden'); 
            document.getElementById('profileMenu').classList.add('hidden');
        }

        function closeReportModal() { 
            document.getElementById('reportModal').classList.add('hidden'); 
        }

        function handleReport(event) { 
            event.preventDefault(); 
            alert('Laporan berhasil dikirim!'); 
            closeReportModal(); 
        }

        function openCommentsModal(event) { 
            if(event) event.stopPropagation(); 
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
        window.onclick = function(event) {
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
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeArtworkPopup();
            }
        });