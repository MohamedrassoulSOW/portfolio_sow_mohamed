/* ==================== typing animation =================*/
const typingEl = document.querySelector(".typing");
const typingStrings = typingEl && typingEl.dataset.strings
    ? typingEl.dataset.strings.split("|").filter(Boolean)
    : ["Développeur web", "Digital Marketer", "Intégrateur web"];

var typed = new Typed(".typing", {
    strings: typingStrings,
    typeSpeed: 50,
    backSpeed: 60,
    loop: true
});

/* ==================== Aside / Sections =================*/
const SECTION_KEY = "portfolioActiveSection";
const nav = document.querySelector(".nav");
const navList = nav.querySelectorAll("li");
const totalNavList = navList.length;
const allSection = document.querySelectorAll(".section");
const totalSection = allSection.length;
const validSections = Array.from(allSection).map((s) => s.id);

function removeBackSection() {
    for (let i = 0; i < totalSection; i++) {
        allSection[i].classList.remove("back-section");
    }
}

function addBackSection(num) {
    if (typeof num === "number" && allSection[num]) {
        allSection[num].classList.add("back-section");
    }
}

function getActiveNavIndex() {
    for (let j = 0; j < totalNavList; j++) {
        const link = navList[j].querySelector("a");
        if (link && link.getAttribute("href")?.startsWith("#") && link.classList.contains("active")) {
            return j;
        }
    }
    return 0;
}

function rememberSection(sectionId) {
    try {
        localStorage.setItem(SECTION_KEY, sectionId);
    } catch (e) {
        /* ignore */
    }
}

function getRememberedSection() {
    try {
        return localStorage.getItem(SECTION_KEY);
    } catch (e) {
        return null;
    }
}

function buildSectionUrl(sectionId) {
    return window.location.pathname + "#" + sectionId;
}

function showSectionById(sectionId, options = {}) {
    const {
        skipAnimation = false,
        updateHash = true,
        pushHistory = false
    } = options;

    if (!sectionId || !validSections.includes(sectionId)) {
        sectionId = "home";
    }

    for (let i = 0; i < totalSection; i++) {
        allSection[i].classList.remove("active");
        if (skipAnimation) {
            allSection[i].style.animation = "none";
        }
    }

    const target = document.getElementById(sectionId);
    if (target) {
        target.classList.add("active");
        if (skipAnimation) {
            void target.offsetWidth;
            setTimeout(() => {
                for (let i = 0; i < totalSection; i++) {
                    allSection[i].style.animation = "";
                }
            }, 50);
        }
    }

    for (let i = 0; i < totalNavList; i++) {
        const link = navList[i].querySelector("a");
        if (!link || !link.getAttribute("href")?.startsWith("#")) {
            continue;
        }
        const linkTarget = link.getAttribute("href").replace("#", "");
        link.classList.toggle("active", linkTarget === sectionId);
    }

    rememberSection(sectionId);

    if (updateHash) {
        const newUrl = buildSectionUrl(sectionId);
        const currentUrl = window.location.pathname + window.location.hash;
        if (currentUrl !== newUrl) {
            if (pushHistory) {
                history.pushState({ section: sectionId }, "", newUrl);
            } else {
                history.replaceState({ section: sectionId }, "", newUrl);
            }
        }
    }
}

function showSection(element, options = {}) {
    const target = element.getAttribute("href").split("#")[1];
    showSectionById(target, options);
}

function resolveInitialSection() {
    const hash = (window.location.hash || "").replace("#", "");
    if (hash && validSections.includes(hash)) {
        return hash;
    }
    const remembered = getRememberedSection();
    if (remembered && validSections.includes(remembered)) {
        return remembered;
    }
    return "home";
}

for (let i = 0; i < totalNavList; i++) {
    const a = navList[i].querySelector("a");
    if (!a || !a.getAttribute("href")?.startsWith("#")) {
        continue;
    }
    a.addEventListener("click", function (e) {
        e.preventDefault();
        const target = this.getAttribute("href").split("#")[1];
        if (document.getElementById(target)?.classList.contains("active")) {
            return;
        }
        removeBackSection();
        addBackSection(getActiveNavIndex());
        showSectionById(target, { updateHash: true, pushHistory: true });
        if (window.innerWidth < 1200) {
            asideSectionTogglerBtn();
        }
    });
}

const logoLink = document.querySelector(".logo a");
if (logoLink) {
    logoLink.addEventListener("click", function (e) {
        e.preventDefault();
        if (document.getElementById("home")?.classList.contains("active")) {
            return;
        }
        removeBackSection();
        addBackSection(getActiveNavIndex());
        showSectionById("home", { updateHash: true, pushHistory: true });
        if (window.innerWidth < 1200 && aside.classList.contains("open")) {
            asideSectionTogglerBtn();
        }
    });
}

const hireMe = document.querySelector(".hire-me");
if (hireMe) {
    hireMe.addEventListener("click", function (e) {
        e.preventDefault();
        const sectionIndex = this.getAttribute("data-section-index");
        removeBackSection();
        addBackSection(Number(sectionIndex));
        showSection(this, { updateHash: true, pushHistory: true });
    });
}

const navTogglerBtn = document.querySelector(".nav-toggler");
const aside = document.querySelector(".aside");

if (navTogglerBtn) {
    navTogglerBtn.addEventListener("click", () => {
        asideSectionTogglerBtn();
    });
}

function asideSectionTogglerBtn() {
    aside.classList.toggle("open");
    navTogglerBtn.classList.toggle("open");
    for (let i = 0; i < totalSection; i++) {
        allSection[i].classList.toggle("open");
    }
}

function restoreSection(skipAnimation = true) {
    showSectionById(resolveInitialSection(), {
        skipAnimation,
        updateHash: true,
        pushHistory: false
    });
}

// Restaure la section (hash URL ou dernière section mémorisée)
restoreSection(true);

window.addEventListener("popstate", function () {
    const hash = (window.location.hash || "").replace("#", "");
    const sectionId = validSections.includes(hash) ? hash : resolveInitialSection();
    removeBackSection();
    showSectionById(sectionId, { updateHash: false, skipAnimation: false });
});

window.addEventListener("hashchange", function () {
    const hash = (window.location.hash || "#home").replace("#", "");
    if (!document.getElementById(hash)?.classList.contains("active")) {
        removeBackSection();
        showSectionById(hash, { updateHash: false });
    }
});

window.addEventListener("load", function () {
    // Nettoie ?logout / ?success sans perdre la section
    if (window.location.search) {
        const params = new URLSearchParams(window.location.search);
        if (params.has("logout") || params.has("success") || params.has("error")) {
            const section = resolveInitialSection();
            const cleanUrl = window.location.pathname + "#" + section;
            setTimeout(() => {
                history.replaceState({ section }, "", cleanUrl);
            }, params.has("success") || params.has("error") ? 2500 : 0);
        }
    }
});
