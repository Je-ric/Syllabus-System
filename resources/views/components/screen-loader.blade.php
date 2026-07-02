<div id="screenLoader" class="fixed inset-0 bg-white flex items-center justify-center transition-opacity duration-500" style="z-index: 99999;">
    <div class="text-center">
        <div class="relative">
            <div class="relative mx-auto mb-4 h-16 w-16">
                <div class="animate-spin rounded-full h-full w-full border-b-2 border-green-600"></div>
                <img src="{{ asset('assets/clsu-logo-green.png') }}" alt="Loading..."
                    class="h-16 w-16 object-contain absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            </div>
            <div class="text-green-600 font-semibold text-lg mb-2">Loading...</div>
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
            window.addEventListener('load', () => {
                this.hideLoader();
            });

            if (document.readyState === 'complete') {
                this.hideLoader();
            }

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
            }
        }

        showLoaderOnNavigation() {
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (link && link.href && !link.href.includes('#') && !link.href.includes('javascript:') && !e.ctrlKey && !e.metaKey) {
                    if (link.href !== window.location.href && link.href.startsWith(window.location.origin)) {
                        this.showLoader();
                    }
                }
            });

            document.addEventListener('submit', (e) => {
                if (e.target.tagName === 'FORM') {
                    this.showLoader();
                }
            });

            window.addEventListener('beforeunload', () => {
                this.showLoader();
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        new ScreenLoader();
    });
</script>