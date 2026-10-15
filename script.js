
const API_URL = "api.php";

const ticketList = document.getElementById("ticketList");
const ticketForm = document.getElementById("ticketForm");
const searchInput = document.getElementById("searchInput");
const statusFilter = document.getElementById("statusFilter");
const priorityFilter = document.getElementById("priorityFilter");
const messageBox = document.getElementById("message");
const submitButton = document.getElementById("submitButton");

let searchTimer;

function escapeHTML(value) {
    return String(value ?? "").replace(/[&<>"']/g, function (character) {
        const entities = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        };
        return entities[character];
    });
}

function showMessage(message, type = "success") {
    messageBox.textContent = message;
    messageBox.className = `message ${type}`;

    if (type === "success") {
        setTimeout(() => {
            messageBox.className = "message";
            messageBox.textContent = "";
        }, 4000);
    }
}

function formatDate(dateString) {
    if (!dateString) return "Date unavailable";

    // MySQL commonly returns a date such as YYYY-MM-DD HH:MM:SS.
    const date = new Date(dateString.replace(" ", "T"));

    if (Number.isNaN(date.getTime())) {
        return dateString;
    }

    return date.toLocaleString("en-IN", {
        day: "2-digit",
        month: "short",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
    });
}

function statusClass(status) {
    if (status === "In Progress") return "status-in-progress";
    if (status === "Resolved") return "status-resolved";
    return "status-open";
}

function priorityClass(priority) {
    if (priority === "High") return "priority-high";
    if (priority === "Medium") return "priority-medium";
    return "priority-low";
}

function updateStatistics(counts) {
    document.getElementById("totalCount").textContent = counts.total ?? 0;
    document.getElementById("openCount").textContent = counts.open ?? 0;
    document.getElementById("progressCount").textContent = counts.progress ?? 0;
    document.getElementById("resolvedCount").textContent = counts.resolved ?? 0;
}

async function loadTickets() {
    ticketList.innerHTML = '<div class="loading">Loading your tickets...</div>';

    const params = new URLSearchParams({
        search: searchInput.value.trim(),
        status: statusFilter.value,
        priority: priorityFilter.value
    });

    try {
        const response = await fetch(`${API_URL}?${params.toString()}`);
        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Could not load tickets.");
        }

        updateStatistics(data.counts);
        renderTickets(data.tickets);
    } catch (error) {
        ticketList.innerHTML = `
            <div class="empty-state">
                <span class="empty-symbol">⚠</span>
                <strong>Unable to load tickets</strong><br>
                ${escapeHTML(error.message)}<br>
                Check that Apache and MySQL are running in XAMPP.
            </div>
        `;
    }
}

function renderTickets(tickets) {
    if (!tickets || tickets.length === 0) {
        ticketList.innerHTML = `
            <div class="empty-state">
                <span class="empty-symbol">✧</span>
                <strong>No tickets found</strong><br>
                Try changing your search or filters, or create a new ticket.
            </div>
        `;
        return;
    }

    ticketList.innerHTML = tickets.map(ticket => {
        const id = Number(ticket.id);
        const statusOptions = ["Open", "In Progress", "Resolved"]
            .map(status => `
                <option value="${status}"
                    ${ticket.status === status ? "selected" : ""}>
                    ${status}
                </option>
            `).join("");

        return `
            <article class="ticket-item">
                <div class="ticket-title-row">
                    <div>
                        <div class="ticket-title">${escapeHTML(ticket.title)}</div>
                        <div class="ticket-id">Ticket #${id}</div>
                    </div>
                    <span class="tag ${statusClass(ticket.status)}">
                        ${escapeHTML(ticket.status)}
                    </span>
                </div>

                <p class="ticket-description">${escapeHTML(ticket.description)}</p>

                <div class="ticket-meta">
                    <span class="tag ${priorityClass(ticket.priority)}">
                        ${escapeHTML(ticket.priority)} priority
                    </span>
                    <span class="tag category-tag">
                        ${escapeHTML(ticket.category)}
                    </span>
                    <span class="ticket-date">${escapeHTML(formatDate(ticket.created_at))}</span>
                </div>

                <div class="status-control">
                    <label for="status-${id}">Update status</label>
                    <select id="status-${id}" data-ticket-id="${id}" class="status-select">
                        ${statusOptions}
                    </select>
                </div>
            </article>
        `;
    }).join("");
}

// Create a ticket using AJAX.
ticketForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    const formData = new FormData(ticketForm);

    const ticket = {
        action: "create",
        title: formData.get("title").trim(),
        category: formData.get("category"),
        priority: formData.get("priority"),
        description: formData.get("description").trim()
    };

    submitButton.disabled = true;
    submitButton.textContent = "Creating ticket...";

    try {
        const response = await fetch(API_URL, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(ticket)
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Ticket could not be created.");
        }

        ticketForm.reset();
        showMessage(data.message || "Ticket created successfully!", "success");

        // Refresh tickets and statistics without reloading the page.
        await loadTickets();

    } catch (error) {
        showMessage(error.message, "error");
    } finally {
        submitButton.disabled = false;
        submitButton.innerHTML = "<span>＋</span> Create support ticket";
    }
});

// Update a ticket's status using event delegation.
ticketList.addEventListener("change", async function (event) {
    if (!event.target.classList.contains("status-select")) return;

    const select = event.target;
    const ticketId = Number(select.dataset.ticketId);
    const newStatus = select.value;

    select.disabled = true;

    try {
        const response = await fetch(API_URL, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                action: "update",
                id: ticketId,
                status: newStatus
            })
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Could not update ticket status.");
        }

        showMessage("Ticket status updated successfully!", "success");
        await loadTickets();

    } catch (error) {
        showMessage(error.message, "error");
        await loadTickets();
    } finally {
        select.disabled = false;
    }
});

// Search after the user pauses typing.
searchInput.addEventListener("input", function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(loadTickets, 300);
});

// Filters refresh the ticket list immediately.
statusFilter.addEventListener("change", loadTickets);
priorityFilter.addEventListener("change", loadTickets);

// Load the dashboard when the page opens.
loadTickets();
