let theme = "";

export function CreateThemeSwitcher() {
    console.log("creation de Theme Switcher")
    const darkModeMql = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)');
    if (darkModeMql && darkModeMql.matches) {
        theme = "dark";
    } else {
        theme = "light";
    }
    if (theme === "dark") {
        document.body.classList.add("dark");
    } else {
        document.body.classList.remove("dark");
    }
    let theme_switcher_div = document.getElementById("theme_switcher_div");
    let imageTheme = (theme === "dark") ? "assets/moon.png" : "assets/sun.png";

    theme_switcher_div.innerHTML = `
        <button id='theme_switcher' onclick='toggleTheme()'>
            <img id='theme-icon' src='/${imageTheme}'>
        </button>
    `;
}

window.toggleTheme = function toggleTheme() {
    theme = (theme === "dark") ? "light" : "dark";
    if (theme === "dark") {
        document.body.classList.add("dark");
    } else {
        document.body.classList.remove("dark");
    }
    let imageTheme = (theme === "dark") ? "/assets/moon.png" : "/assets/sun.png";
    document.getElementById("theme-icon").src = imageTheme;
};
