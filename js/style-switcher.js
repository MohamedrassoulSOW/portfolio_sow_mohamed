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
const alternateStyles = document.querySelectorAll(".alternate-style");
function setActiveStyle(color) {
    alternateStyles.forEach((style) => {
        if (color === style.getAttribute("title")) {
            style.removeAttribute("disabled");
        } else {
            style.setAttribute("disabled", "true");
        }
    });
}

/* ==================== Thème light and dark mode =================*/
const dayNight = document.querySelector(".day-night");
if (dayNight) {
    dayNight.addEventListener("click", () => {
        dayNight.querySelector("i").classList.toggle("fa-sun");
        dayNight.querySelector("i").classList.toggle("fa-moon");
        document.body.classList.toggle("dark");
    });

    window.addEventListener("load", () => {
        if (document.body.classList.contains("dark")) {
            dayNight.querySelector("i").classList.add("fa-sun");
        } else {
            dayNight.querySelector("i").classList.add("fa-moon");
        }
    });
}
