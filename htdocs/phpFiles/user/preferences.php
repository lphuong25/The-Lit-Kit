<?php
  session_start();
  require_once 'db_connect.php';

  if (!isset($_SESSION['user_id'])) {
    header("Location: signIn.php");
    exit();
}

  $all_genres = $conn->query("SELECT genreName FROM genres ORDER BY genreName ASC");

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_SESSION['user_id'];

    // 1. Wipe existing preferences
    $delete = $conn->prepare("DELETE FROM prefers WHERE userID = ?");
    $delete->bind_param("i", $userID);
    $delete->execute();

    // 2. Get the array of genres and filter out empty inputs
    $submitted_genres = array_filter($_POST['genre']); // Removes null/empty strings

    foreach ($submitted_genres as $gName) {
        // Get genreID from genre name
        $getID = $conn->prepare("SELECT genreID FROM genres WHERE genreName = ?");
        $getID->bind_param("s", $gName);
        $getID->execute();
        $result = $getID->get_result();

        if ($row = $result->fetch_assoc()) {
            $genreID = $row['genreID'];

            // Insert into prefers table
            $insert = $conn->prepare("INSERT INTO prefers (userID, genreID) VALUES (?, ?)");
            $insert->bind_param("ii", $userID, $genreID);
            $insert->execute();
        }
    }
    header("Location: mainPage.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>The Lit Kit — Discover Literature</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&family=EB+Garamond:wght@400;500&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --dark: #1a1a1a;
      --border: #e0e0e0;
      --input-border: #c8c8c8;
      --bg: #ffffff;
    }

    html, body { height: 100%; }

    body {
      font-family: 'EB Garamond', Georgia, serif;
      background: var(--bg);
      color: var(--dark);
      display: flex;
      flex-direction: column;
    }

    /* layout for top header */
    .top-bar {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 18px 60px;
      border-bottom: 1px solid var(--border);
      flex-shrink: 0;
    }

    .logo { text-decoration: none; }

    .logo-text {
      font-family: 'Playfair Display', Georgia, serif;
      font-style: italic;
      font-size: 2.0rem;
      color: var(--dark);
      letter-spacing: 0.01em;
    }

    /* main split layout */
    .split {
      display: flex;
      flex: 1;
    }

    .split-left {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 60px 70px;
    }

    .form-wrap {
      width: 100%;
      max-width: 560px;
    }

    .form-title {
      font-family: 'Playfair Display', Georgia, serif;
      font-size: 3.6rem;
      font-weight: 400;
      line-height: 1.2;
      color: var(--dark);
      margin-bottom: 32px;
    }

    .divider {
      border: none;
      border-top: 1px solid var(--input-border);
      margin-bottom: 32px;
    }

    .subtitle {
      font-size: 1.3rem;
      color: var(--dark);
      margin-bottom: 42px;
    }

    .dropdown-row {
      display: flex;
      align-items: center;
      gap: 24px;
      margin-bottom: 34px;
    }

    .dropdown-row label {
      font-size: 1.25rem;
      color: var(--dark);
      white-space: nowrap;
      min-width: 148px;
    }

    .select-wrap {
      flex: 1;
      position: relative;
    }

    /* dropdown styling */
    /* Update this selector to include inputs */
    .custom-dropdown {
    flex: 1;
    position: relative;
}

.genre-input {
    width: 100%;
    font-family: 'EB Garamond', Georgia, serif;
    font-size: 1.15rem;
    padding: 14px 44px 14px 16px;
    border: 1px solid var(--input-border);
    border-radius: 3px;
    background: #fff;
    color: var(--dark);
    outline: none;
    transition: border-color 0.2s;
    cursor: pointer;
}

.genre-input:focus {
    border-color: var(--dark);
}

/* Dropdown arrow */
.custom-dropdown::after {
    content: '▾';
    font-size: 1.1rem;
    position: absolute;
    right: 16px;
    top: 19px;
    color: var(--dark);
    pointer-events: none;
    transition: transform 0.2s;
}

.custom-dropdown.open::after {
    transform: rotate(180deg);
}

/* The actual dropdown list */
.genre-options {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;

    background: #fff;
    border: 1px solid var(--input-border);
    border-radius: 3px;

    max-height: 240px;
    overflow-y: auto;

    z-index: 1000;

    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.10);
}

  /* Show dropdown */
  .custom-dropdown.open .genre-options {
      display: block;
  }

  /* Individual genre */
  .genre-option {
      padding: 11px 16px;

      font-family: 'EB Garamond', Georgia, serif;
      font-size: 1.05rem;

      color: var(--dark);
      background: #fff;

      cursor: pointer;

      border-bottom: 1px solid #f0f0f0;

      transition: background 0.15s;
  }

  .genre-option:last-child {
      border-bottom: none;
  }

  .genre-option:hover {
      background: #f5f3f0;
  }

  /* Genre already selected in another box */
  .genre-option.selected {
      color: #aaa;
      background: #f5f5f5;
      cursor: not-allowed;
  }

  /* Hide genres that don't match search */
  .genre-option.hidden {
      display: none;
  }

    /* next button styling */
    .btn-next {
      display: inline-block;
      margin-top: 20px;
      background: var(--dark);
      color: #fff;
      font-family: 'EB Garamond', Georgia, serif;
      font-size: 1.2rem;
      letter-spacing: 0.04em;
      padding: 16px 72px;
      border: none;
      border-radius: 3px;
      cursor: pointer;
      transition: background 0.2s, transform 0.15s;
    }

    .btn-next:hover { background: #333; transform: translateY(-1px); }
    .btn-next:active { transform: translateY(0); }

    .split-right {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f0ece6;
      color: #aaa;
      font-family: 'EB Garamond', Georgia, serif;
      font-size: 1.3rem;
      letter-spacing: 0.08em;
    }
  </style>
</head>
<body>
  <header class="top-bar">
    <a href="#" class="logo">
      <span class="logo-text">The Lit Kit</span>
    </a>
  </header>

  <div class="split">
    <div class="split-left">
      <div class="form-wrap">
        <h1 class="form-title">Discover Literature<br>You'll Love</h1>
        <hr class="divider" />
        <p class="subtitle">Choose your top 3 genres:</p>

        <form method="POST">
          <?php 
          $genre_list = [];

          $all_genres->data_seek(0);

          while ($row = $all_genres->fetch_assoc()) {
              $genre_list[] = $row['genreName'];
          }
          ?>

          <?php for ($i = 1; $i <= 3; $i++): ?>

              <div class="dropdown-row">

                  <label for="genre<?php echo $i; ?>">
                      <?php echo ($i == 1) ? 'First' : (($i == 2) ? 'Second' : 'Third'); ?> Choice:
                  </label>

                  <div class="custom-dropdown">

                      <input
                          type="text"
                          id="genre<?php echo $i; ?>"
                          name="genre[]"
                          placeholder="Type to search..."
                          autocomplete="off"
                          class="genre-input"
                      >

                      <div class="genre-options">

                          <?php foreach ($genre_list as $genre): ?>

                              <div
                                  class="genre-option"
                                  data-value="<?php echo htmlspecialchars($genre); ?>"
                              >
                                  <?php echo htmlspecialchars($genre); ?>
                              </div>

                          <?php endforeach; ?>

                      </div>

                  </div>

              </div>

          <?php endfor; ?>

          <button type="submit" class="btn-next">Next</button>
        </form>
      </div>
    </div>

    <div class="split-right">
        <img src="../../images/librairie-romantique-1600.jpg" style="width:100%; height:100%; object-fit:cover;">
    </div>
  </div>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const dropdowns = document.querySelectorAll(".custom-dropdown");

    dropdowns.forEach(dropdown => {

        const input = dropdown.querySelector(".genre-input");
        const options = dropdown.querySelectorAll(".genre-option");

        // Open dropdown when clicked/focused
        input.addEventListener("focus", function () {
            closeOtherDropdowns(dropdown);
            dropdown.classList.add("open");
            filterOptions();
        });

        input.addEventListener("click", function () {
            closeOtherDropdowns(dropdown);
            dropdown.classList.add("open");
            filterOptions();
        });

        // Search while typing
        input.addEventListener("input", function () {
            dropdown.classList.add("open");
            filterOptions();
            updateSelectedGenres();
        });

        // Filter the list
        function filterOptions() {

            const search = input.value.toLowerCase().trim();

            options.forEach(option => {

                const genre = option.dataset.value.toLowerCase();

                if (genre.includes(search)) {
                    option.classList.remove("hidden");
                } else {
                    option.classList.add("hidden");
                }

            });
        }

        // Select a genre
        options.forEach(option => {

            option.addEventListener("click", function () {

                // Don't allow already-selected genres
                if (option.classList.contains("selected")) {
                    return;
                }

                input.value = option.dataset.value;

                dropdown.classList.remove("open");

                updateSelectedGenres();
            });

        });

    });


    // Close all other dropdowns
    function closeOtherDropdowns(currentDropdown) {

        document.querySelectorAll(".custom-dropdown").forEach(dropdown => {

            if (dropdown !== currentDropdown) {
                dropdown.classList.remove("open");
            }

        });

    }


    // Disable/mute genres already selected in another box
    function updateSelectedGenres() {

        const inputs = document.querySelectorAll(".genre-input");

        const selected = [];

        inputs.forEach(input => {

            const value = input.value.trim();

            if (value !== "") {
                selected.push(value);
            }

        });


        document.querySelectorAll(".genre-option").forEach(option => {

            const value = option.dataset.value;

            const currentDropdown = option.closest(".custom-dropdown");

            const currentInput =
                currentDropdown.querySelector(".genre-input");


            // If this is the genre currently selected
            // in THIS dropdown, don't mute it.
            if (currentInput.value === value) {

                option.classList.remove("selected");

            }

            // If another dropdown selected it
            else if (selected.includes(value)) {

                option.classList.add("selected");

            }

            else {

                option.classList.remove("selected");

            }

        });

    }


    // Click outside = close dropdown
    document.addEventListener("click", function (event) {

        if (!event.target.closest(".custom-dropdown")) {

            document.querySelectorAll(".custom-dropdown")
                .forEach(dropdown => {
                    dropdown.classList.remove("open");
                });

        }

    });


    // Initial state
    updateSelectedGenres();

});
</script>

  <!-- Note: Removed the old JavaScript as it is incompatible with datalists -->
</body>
</html>