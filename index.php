<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Литературный ассистент</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header>
    <div class="tabs">
      <button class="tab-button active" onclick="switchTab('search')">Найти книги по описанию</button>
      <button class="tab-button" onclick="switchTab('generate')">Сгенерировать историю</button>
    </div>
    <div class="toggle-theme" onclick="toggleTheme()" id="themeIcon">🌙</div>
  </header>

  <div class="content">
    <div id="search" class="tab-content">
      <form id="book-search-form" onsubmit="submitSearch(event)">
        <h2>Найти книги по описанию</h2>
        <label for="description">Опиши книгу, которую хочешь найти <span style="color: red">*</span></label>
        <textarea id="description" name="description" rows="4" required></textarea><br>

        <label for="genre">Какой жанр хотел бы прочесть?</label>
        <input type="text" id="genre" name="genre" placeholder="например, фэнтези, детектив"><br>

        <label for="length">Какой объем?</label>
        <input type="text" id="length" name="length" placeholder="короткая, длинная, роман и т.д."><br>

        <label for="country">Литература какой страны тебя интересует?</label>
        <input type="text" id="country" name="country" placeholder="например, Франция, Япония"><br>

        <label for="age">Какие возрастные ограничения?</label>
        <input type="text" id="age" name="age" placeholder="0+, 16+, 18+ и т.д."><br>

        <button type="submit">Найти книги</button>
      </form>
      <div class="output" id="search-results"></div>
    </div>

    <div id="generate" class="tab-content hidden">
      <form id="generate-form" onsubmit="generateStory(event)">
        <h2>Сгенерировать историю</h2>
        <label for="storyPrompt">Введите описание для генерации истории</label>
        <textarea id="storyPrompt" name="storyPrompt" rows="4" required></textarea><br>
        <button type="submit">Сгенерировать</button>
      </form>
      <div class="output" id="storyOutput"></div>
    </div>
  </div>

  <script src="assets/script.js"></script>
</body>
</html>
