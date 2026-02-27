{{-- Screen Loader Component --}}
<div id="screenLoader" class="fixed inset-0 z-99999 bg-white flex items-center justify-center transition-opacity duration-500">
    <div class="text-center">
        <div class="relative">
            {{-- Spinning Logo --}}

            <div class="relative mx-auto mb-4 h-16 w-16">
                <div class="animate-spin rounded-full h-full w-full border-b-2 border-green-600"></div>
                <img src="{{ asset('assets/clsu-logo-green.png') }}" alt="Loading..."
                    class="h-16 w-16 object-contain absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            </div>

            {{-- Loading Text --}}
            <div class="text-green-600 font-semibold text-lg mb-2">Loading...</div>
            {{-- Loading Dots --}}
            <div class="flex justify-center space-x-1">
                <div class="w-2 h-2 bg-green-600 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-green-600 rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
                <div class="w-2 h-2 bg-green-600 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    class ScreenLoader {
        constructor() {
            this.loader = document.getElementById('screenLoader');
            this.init();
        }

        init() {
            // Hide loader when page is fully loaded
            window.addEventListener('load', () => {
                this.hideLoader();
            });

            // Hide loader when DOM is ready (fallback)
            if (document.readyState === 'complete') {
                this.hideLoader();
            }

            // Show loader on navigation
            this.showLoaderOnNavigation();
        }

        showLoader() {
            if (this.loader) {
                this.loader.style.opacity = '1';
                this.loader.style.pointerEvents = 'auto';
            }
        }

        hideLoader() {
            if (this.loader) {
                this.loader.style.opacity = '0';
                this.loader.style.pointerEvents = 'none';
                // Remove from DOM after animation
                setTimeout(() => {
                    if (this.loader && this.loader.style.opacity === '0') {
                        this.loader.remove();
                    }
                }, 500);
            }
        }

        showLoaderOnNavigation() {
            // Show loader on link clicks
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (link && link.href && !link.href.includes('#') && !link.href.includes('javascript:') && !e.ctrlKey && !e.metaKey) {
                    // Don't show loader for same-page links or external links
                    if (link.href !== window.location.href && link.href.startsWith(window.location.origin)) {
                        this.showLoader();
                    }
                }
            });

            // Show loader on form submissions
            document.addEventListener('submit', (e) => {
                if (e.target.tagName === 'FORM') {
                    this.showLoader();
                }
            });

            // Show loader on browser back/forward
            window.addEventListener('beforeunload', () => {
                this.showLoader();
            });
        }
    }

    // Initialize screen loader when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
        new ScreenLoader();
    });
</script>

{{--
Usage: <x-screen-loader />
       Note: This component automatically handles page loading states

Features:
- Shows on page load, navigation, and form submissions
- Automatically hides when page is ready
- Includes spinning logo and animated dots
- Handles browser back/forward navigation

Used in:
- resources/views/layouts/sidebar_final.blade.php
--}}
