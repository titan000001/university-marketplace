document.addEventListener('DOMContentLoaded', () => {
    const appNode = document.getElementById('app');

    if (appNode) {
        appNode.innerHTML = `
            <div class="dashboard-wrapper">
                <!-- Phase 6: Hero Banner & Welcome Section (Lead Developer: Hiya Moni) -->
                <section class="banner-section">
                    <h1>Welcome, Student</h1>
                    <p>Buy and sell textbooks, electronics, study furniture, and other gear safely. Here is your campus hub performance index for today.</p>
                    <div class="banner-buttons">
                        <button id="browse-btn" class="btn btn-primary" onclick="alert('Navigating to marketplace listings...')">
                            <i class="fas fa-shopping-bag"></i> Browse Items
                        </button>
                        <a href="#/sell" class="btn btn-secondary">
                            <i class="fas fa-tags"></i> Start Selling
                        </a>
                    </div>
                </section>
            </div>
        `;
    }
});
