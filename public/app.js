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

    // Render Sabir Rahman's statistics section
    renderStatsSection();
});

// ==========================================
// Sabir Rahman's Declarative Component (Week 3)
// ==========================================

/**
 * Declarative Component Function: StatsCardComponent
 * @param {string} title - The metric title
 * @param {number|string} count - The statistical count
 * @param {string} statusColor - Tailwind text color class identifier
 * @returns {string} Styled HTML template literal
 */
function StatsCardComponent(title, count, statusColor = 'indigo-600') {
    const colorClass = statusColor.startsWith('text-') ? statusColor : `text-${statusColor}`;
    return `
        <div class="stat-card bg-white p-6 rounded-xl shadow-md border border-gray-100 text-center transition-all hover:shadow-lg">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">${title}</h3>
            <p class="stat-number text-4xl font-extrabold ${colorClass}">${count}</p>
        </div>
    `;
}

// Array of statistics objects
const uniMarketStats = [
    { title: 'Total Listings', count: 45, statusColor: 'indigo-600' },
    { title: 'Active Deals', count: 120, statusColor: 'emerald-600' },
    { title: 'Verified Users', count: '1,250', statusColor: 'blue-600' }
];

/**
 * Function: renderStatsSection
 * Creates a parent div with responsive CSS Grid (grid-cols-1 md:grid-cols-3),
 * iterates over uniMarketStats using .map(), passes data into StatsCardComponent,
 * and appends the result to #app.
 */
function renderStatsSection() {
    const appNode = document.getElementById('app');
    if (!appNode) return;

    const statsGridContainer = document.createElement('div');
    statsGridContainer.className = 'stats-grid grid grid-cols-1 md:grid-cols-3 gap-6 my-8 px-4 max-w-7xl mx-auto';

    const cardsHtml = uniMarketStats.map(stat => 
        StatsCardComponent(stat.title, stat.count, stat.statusColor)
    ).join('');

    statsGridContainer.innerHTML = cardsHtml;
    appNode.appendChild(statsGridContainer);
}
