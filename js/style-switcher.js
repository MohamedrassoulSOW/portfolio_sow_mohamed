/* ==================== toggle style switcher =================*/
const styleSwitcherToggle = document.querySelector(".style-switcher-toggler");
if (styleSwitcherToggle) {
    styleSwitcherToggle.addEventListener("click", () => {
        document.querySelector(".style-switcher").classList.toggle("open");
    });
}

window.addEventListener("scroll", () => {
    const switcher = document.querySelector(".style-switcher");
    if (switcher && switcher.classList.contains("open")) {
        switcher.classList.remove("open");
    }
});

/* ==================== Thème Colors =================*/
const THEME_COLOR_KEY = "portfolioThemeColor";
const THEME_MODE_KEY = "portfolioThemeMode";
const alternateStyles = document.querySelectorAll(".alternate-style");

function setActiveStyle(color) {
    alternateStyles.forEach((style) => {
        if (color === style.getAttribute("title")) {
            style.removeAttribute("disabled");
        } else {
            style.setAttribute("disabled", "true");
        }
    });
    try {
        localStorage.setItem(THEME_COLOR_KEY, color);
    } catch (e) { /* ignore */ }
}

/* ==================== Thème light and dark mode =================*/
function applyThemeMode(mode) {
    const isDark = mode === "dark";
    document.body.classList.toggle("dark", isDark);
    const dayNight = document.querySelector(".day-night");
    if (!dayNight) return;
    const icon = dayNight.querySelector("i");
    if (!icon) return;
    icon.classList.toggle("fa-sun", isDark);
    icon.classList.toggle("fa-moon", !isDark);
}

function getSavedThemeMode() {
    try {
        return localStorage.getItem(THEME_MODE_KEY) || "light";
    } catch (e) {
        return "light";
    }
}

const dayNight = document.querySelector(".day-night");
if (dayNight) {
    dayNight.addEventListener("click", () => {
        const nextMode = document.body.classList.contains("dark") ? "light" : "dark";
        applyThemeMode(nextMode);
        try {
            localStorage.setItem(THEME_MODE_KEY, nextMode);
        } catch (e) { /* ignore */ }
    });
}

window.addEventListener("load", () => {
    applyThemeMode(getSavedThemeMode());

    try {
        const savedColor = localStorage.getItem(THEME_COLOR_KEY);
        if (savedColor) setActiveStyle(savedColor);
    } catch (e) { /* ignore */ }
});
