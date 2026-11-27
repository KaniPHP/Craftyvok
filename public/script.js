const menuToggle = document.querySelector(".menu-toggle");
const primaryNav = document.querySelector("#primary-nav");

if (menuToggle && primaryNav) {
  menuToggle.addEventListener("click", () => {
    const isExpanded = menuToggle.getAttribute("aria-expanded") === "true";
    menuToggle.setAttribute("aria-expanded", String(!isExpanded));
    menuToggle.setAttribute("aria-label", isExpanded ? "Open menu" : "Close menu");
    primaryNav.classList.toggle("is-open", !isExpanded);
  });

  primaryNav.addEventListener("click", (event) => {
    if (event.target instanceof HTMLAnchorElement) {
      menuToggle.setAttribute("aria-expanded", "false");
      menuToggle.setAttribute("aria-label", "Open menu");
      primaryNav.classList.remove("is-open");
    }
  });
}

const year = document.querySelector("#year");
if (year) year.textContent = String(new Date().getFullYear());

const searchToggle = document.querySelector(".search-toggle");
const searchForm = document.querySelector("#site-search");
const giftSearch = document.querySelector("#gift-search");
const searchClose = document.querySelector(".search-close");

if (searchToggle && searchForm && giftSearch && searchClose) {
  searchToggle.addEventListener("click", () => {
    const isOpen = !searchForm.hidden;
    searchForm.hidden = isOpen;
    searchToggle.setAttribute("aria-expanded", String(!isOpen));
    if (!isOpen) giftSearch.focus();
  });

  giftSearch.addEventListener("input", () => {
    const query = giftSearch.value.trim().toLocaleLowerCase();
    document.querySelectorAll(".category-card").forEach((card) => {
      card.hidden = query !== "" && !card.textContent.toLocaleLowerCase().includes(query);
    });
  });

  searchClose.addEventListener("click", () => {
    giftSearch.value = "";
    giftSearch.dispatchEvent(new Event("input"));
    searchForm.hidden = true;
    searchToggle.setAttribute("aria-expanded", "false");
  });
}

const chatWidget = document.querySelector(".chat-widget");

if (chatWidget) {
  const chatToggle = chatWidget.querySelector(".chat-toggle");
  const chatPanel = chatWidget.querySelector(".chat-panel");
  const chatClose = chatWidget.querySelector(".chat-close");
  const chatForm = chatWidget.querySelector(".chat-form");
  const chatInput = chatWidget.querySelector("#chat-message");
  const chatMessages = chatWidget.querySelector(".chat-messages");
  const chatStatus = chatWidget.querySelector(".chat-status");
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  const tokenKey = "craftyvok-chat-token";
  const conversationKey = "craftyvok-chat-conversation";
  const itemKey = "craftyvok-chat-item";
  let token = localStorage.getItem(tokenKey);
  let conversationId = localStorage.getItem(conversationKey);
  let currentItem = localStorage.getItem(itemKey) || "a gift";
  let pollTimer;

  const messageUrl = (template) => template.replace("__ID__", encodeURIComponent(conversationId));

  function clearConversation() {
    token = null;
    conversationId = null;
    localStorage.removeItem(tokenKey);
    localStorage.removeItem(conversationKey);
  }

  function showPanel(open) {
    chatPanel.hidden = !open;
    chatToggle.setAttribute("aria-expanded", String(open));
    if (open) chatInput.focus();
  }

  function renderMessages(messages) {
    const welcome = chatMessages.querySelector(".chat-welcome");
    if (welcome) welcome.remove();
    chatMessages.replaceChildren();

    messages.forEach((message) => {
      const bubble = document.createElement("p");
      bubble.className = `chat-bubble chat-bubble-${message.sender}`;
      bubble.textContent = message.body;
      chatMessages.append(bubble);
    });

    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  async function sendRequest(url, method, data, chatToken) {
    const headers = { Accept: "application/json", "Content-Type": "application/json", "X-CSRF-TOKEN": csrfToken };
    if (chatToken) headers["X-Chat-Token"] = chatToken;
    const response = await fetch(url, {
      method,
      headers,
      credentials: "same-origin",
      body: data ? JSON.stringify(data) : undefined,
    });
    const result = await response.json();
    if (!response.ok) {
      const error = new Error(result.message || "Chat could not be updated. Please try again.");
      error.status = response.status;
      throw error;
    }
    return result;
  }

  async function refreshMessages() {
    if (!conversationId || !token || document.hidden) return;

    try {
      const result = await sendRequest(messageUrl(chatWidget.dataset.messagesUrl), "GET", null, token);
      renderMessages(result.messages);
      chatStatus.textContent = "";
    } catch (error) {
      if (error.status === 404) {
        clearConversation();
        chatStatus.textContent = "Your previous chat has expired. Send a new message to start again.";
        return;
      }
      chatStatus.textContent = error.message;
    }
  }

  chatToggle.addEventListener("click", () => {
    const open = chatPanel.hidden;
    showPanel(open);
    if (open) refreshMessages();
  });
  chatClose.addEventListener("click", () => showPanel(false));

  document.querySelectorAll(".js-chat-open").forEach((button) => {
    button.addEventListener("click", () => {
      currentItem = button.dataset.chatItem || "a gift";
      localStorage.setItem(itemKey, currentItem);
      chatInput.value = `Hi! Could you share the price and details for ${currentItem}?`;
      showPanel(true);
    });
  });

  chatForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    const message = chatInput.value.trim();
    if (!message) return;

    chatInput.disabled = true;
    chatStatus.textContent = "";
    try {
      if (!conversationId || !token) {
        const result = await sendRequest(chatWidget.dataset.startUrl, "POST", {
          token,
          item: currentItem,
          message,
        });
        token = result.token;
        conversationId = String(result.conversationId);
        localStorage.setItem(itemKey, currentItem);
        localStorage.setItem(tokenKey, token);
        localStorage.setItem(conversationKey, conversationId);
        renderMessages(result.messages);
      } else {
        await sendRequest(messageUrl(chatWidget.dataset.sendUrl), "POST", {
          message,
          item: currentItem,
        }, token);
        await refreshMessages();
      }
      chatInput.value = "";
    } catch (error) {
      if (error.status === 404) clearConversation();
      chatStatus.textContent = error.message;
    } finally {
      chatInput.disabled = false;
      chatInput.focus();
    }
  });

  pollTimer = window.setInterval(refreshMessages, 5000);
  window.addEventListener("pagehide", () => window.clearInterval(pollTimer), { once: true });
}
