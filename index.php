
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Support | Ticket Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon">✦</div>
                <div>
                    <h2>HelpDesk<span>.</span></h2>
                    <p>IT SUPPORT CENTER</p>
                </div>
            </div>

            <p class="menu-label">WORKSPACE</p>
            <a class="nav-link active" href="#dashboard">▦ &nbsp; Dashboard</a>
            <a class="nav-link" href="#tickets">▤ &nbsp; All Tickets</a>
            <a class="nav-link" href="#create-ticket">＋ &nbsp; Create Ticket</a>

            <div class="sidebar-bottom">
                <div class="help-card">
                    <div class="help-icon">✧</div>
                    <h3>Need assistance?</h3>
                    <p>Create a ticket and our IT team will help you out.</p>
                    <a href="#create-ticket">Get support →</a>
                </div>
                <p class="sidebar-footer">IT SUPPORT PORTAL · 2026</p>
            </div>
        </aside>

        <main class="main-content" id="dashboard">
            <header class="topbar">
                <div>
                    <p class="eyebrow">WORKSPACE / OVERVIEW</p>
                    <h1>Ticket Dashboard <span class="wave">✦</span></h1>
                    <p class="subtitle">Track issues. Get things back on track.</p>
                </div>
                <div class="profile">
                    <div class="online-dot"></div>
                    <div class="avatar">IT</div>
                    <div>
                        <strong>Support Team</strong>
                        <small>Dashboard</small>
                    </div>
                </div>
            </header>

            <section class="stats-grid" aria-label="Ticket statistics">
                <article class="stat-card stat-total">
                    <div class="stat-top"><span>Total tickets</span><span class="stat-icon">▤</span></div>
                    <h2 id="totalCount">0</h2>
                    <p>All reported issues</p>
                </article>
                <article class="stat-card stat-open">
                    <div class="stat-top"><span>Open tickets</span><span class="stat-icon">◷</span></div>
                    <h2 id="openCount">0</h2>
                    <p>Waiting for attention</p>
                </article>
                <article class="stat-card stat-progress">
                    <div class="stat-top"><span>In progress</span><span class="stat-icon">↻</span></div>
                    <h2 id="progressCount">0</h2>
                    <p>Currently being handled</p>
                </article>
                <article class="stat-card stat-resolved">
                    <div class="stat-top"><span>Resolved</span><span class="stat-icon">✓</span></div>
                    <h2 id="resolvedCount">0</h2>
                    <p>Issues fixed successfully</p>
                </article>
            </section>

            <section class="content-grid">
                <section class="panel ticket-panel" id="tickets">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">YOUR WORK QUEUE</p>
                            <h2>Support tickets</h2>
                        </div>
                        <span class="live-label"><span></span> LIVE DATA</span>
                    </div>

                    <div class="filters">
                        <div class="search-box">
                            <span>⌕</span>
                            <input type="search" id="searchInput" placeholder="Search ticket titles...">
                        </div>
                        <select id="statusFilter" aria-label="Filter by status">
                            <option value="">All statuses</option>
                            <option value="Open">Open</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Resolved">Resolved</option>
                        </select>
                        <select id="priorityFilter" aria-label="Filter by priority">
                            <option value="">All priorities</option>
                            <option value="High">High priority</option>
                            <option value="Medium">Medium priority</option>
                            <option value="Low">Low priority</option>
                        </select>
                    </div>

                    <div id="message" class="message" role="status" aria-live="polite"></div>
                    <div id="ticketList" class="ticket-list">
                        <div class="loading">Loading your tickets...</div>
                    </div>
                </section>

                <section class="panel create-panel" id="create-ticket">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">RAISE AN ISSUE</p>
                            <h2>Create a ticket</h2>
                        </div>
                        <div class="create-icon">＋</div>
                    </div>

                    <p class="form-intro">Tell us what went wrong. We'll help you sort it out.</p>

                    <form id="ticketForm">
                        <label for="title">Ticket title <span>*</span></label>
                        <input type="text" id="title" name="title"
                               placeholder="e.g. Laptop won't start" minlength="3" maxlength="150" required>

                        <div class="form-row">
                            <div>
                                <label for="category">Category <span>*</span></label>
                                <select id="category" name="category" required>
                                    <option value="">Choose category</option>
                                    <option value="Hardware">Hardware</option>
                                    <option value="Software">Software</option>
                                    <option value="Network">Network</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label for="priority">Priority <span>*</span></label>
                                <select id="priority" name="priority" required>
                                    <option value="">Choose level</option>
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                        </div>

                        <label for="description">Issue description <span>*</span></label>
                        <textarea id="description" name="description" rows="5"
                                  placeholder="Describe the issue and what you have tried..." minlength="5" maxlength="2000" required></textarea>
                        <div class="character-note">Please include enough detail for the team to help.</div>

                        <button class="submit-button" id="submitButton" type="submit">
                            <span>＋</span> Create support ticket
                        </button>
                        <p class="form-footnote">Fields marked * are required.</p>
                    </form>
                </section>
            </section>

            <footer class="page-footer">
                <span>✦ HelpDesk <span class="footer-dot">·</span> IT Support Portal</span>
                <span>Made to keep your work moving.</span>
            </footer>
        </main>
    </div>

    <script src="script.js"></script>
</body>
</html>
