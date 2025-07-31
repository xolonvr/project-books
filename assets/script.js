document.addEventListener("DOMContentLoaded", () => {
  const tabButtons = document.querySelectorAll(".tab-button");
  const themeIcon = document.getElementById("themeIcon");

  // Tab switcher
  window.switchTab = function(tabName) {
    tabButtons.forEach(btn => btn.classList.remove("active"));
    document.querySelector(`.tab-button[onclick*="${tabName}"]`).classList.add("active");

    document.querySelectorAll(".tab-content").forEach(tab => tab.classList.add("hidden"));
    document.getElementById(tabName).classList.remove("hidden");
  };

  // Theme toggle
  window.toggleTheme = function () {
    document.body.classList.toggle("dark-theme");
    const isDark = document.body.classList.contains("dark-theme");
    themeIcon.textContent = isDark ? "☀️" : "🌙";
  };

  // Story generation
  window.generateStory = function (event) {
  event.preventDefault();
  console.log("Форма пошла");

  const prompt = document.getElementById("storyPrompt").value.trim();
  console.log("Промт:", prompt);
  const output = document.getElementById("storyOutput");
  if (!prompt) {
    output.textContent = "Пожалуйста, введите описание.";
    return;
  }

  output.textContent = "Генерация истории...";

  fetch("generate.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ storyPrompt: prompt })
  })
  .then(response => response.json())
  .then(data => {
    if (data.story) {
      output.textContent = data.story;
    } else if (data.error) {
      output.textContent = "Ошибка: " + data.error;
    } else {
      output.textContent = "Неизвестный ответ от сервера.";
    }
  })
  .catch(err => output.textContent = "Ошибка генерации: " + err);
};

  // Book search
  window.submitSearch = function (event) {
    event.preventDefault();

    const form = document.getElementById("book-search-form");
    const results = document.getElementById("search-results");

    const formData = new FormData(form);
    results.textContent = "Поиск книг...";

    fetch("search.php", {
      method: "POST",
      body: formData
    })
    .then(response => response.json())
    .then(data => renderSearchResults(data))
    .catch(err => results.textContent = "Ошибка поиска: " + err);
  };

  // Render search results
  function renderSearchResults(data) {
    const container = document.getElementById("search-results");
    container.innerHTML = "";

    if (!data.books || data.books.length === 0) {
        container.innerHTML = "<p>Ничего не найдено.</p>";
        return;
    }

    data.books.forEach(book => {
        const card = document.createElement("div");
        card.classList.add("book-card");

        card.innerHTML = `
          <div class="book-card-inner">
            ${book.thumbnail ? `<img src="${book.thumbnail}" alt="Обложка книги" class="book-cover">` : ''}
            <div class="book-info">
              <h3>${book.title}</h3>
              <p><strong>Автор:</strong> ${book.author}</p>
              <p><strong>Год:</strong> ${book.year}</p>
              <p><strong>Описание:</strong> ${book.description}</p>
            </div>
          </div>
        `;

        container.appendChild(card);
    });
  }
});
