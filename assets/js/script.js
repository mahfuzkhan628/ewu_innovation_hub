/* =========================
   Page Load Smooth Effect
========================= */
window.addEventListener("load", () => {
    document.body.classList.add("loaded");
});

/* =========================
   Mobile Sidebar / Menu Toggle (NEWLY ADDED)
========================= */
document.addEventListener("DOMContentLoaded", () => {
    const mobileMenuToggle = document.getElementById("mobileMenuToggle");
    const navLinks = document.getElementById("navLinks");

    if (mobileMenuToggle && navLinks) {
        mobileMenuToggle.addEventListener("click", (e) => {
            e.stopPropagation();
            navLinks.classList.toggle("show");
            // টগল এর সময় আইকন ☰ থেকে ✖ এ চেঞ্জ করার জন্য
            if (navLinks.classList.contains("show")) {
                mobileMenuToggle.textContent = "✕";
            } else {
                mobileMenuToggle.textContent = "☰";
            }
        });

        // স্ক্রিনের বাইরে ক্লিক করলে মেনু বন্ধ হয়ে যাবে
        document.addEventListener("click", (e) => {
            if (!mobileMenuToggle.contains(e.target) && !navLinks.contains(e.target)) {
                navLinks.classList.remove("show");
                mobileMenuToggle.textContent = "☰";
            }
        });

        // যেকোনো নেভবারে লিংকে ক্লিক করলে মেনু অটো বন্ধ হবে
        navLinks.querySelectorAll("a").forEach(link => {
            link.addEventListener("click", () => {
                navLinks.classList.remove("show");
                mobileMenuToggle.textContent = "☰";
            });
        });
    }
});

/* =========================
   Navbar Dynamic Styling
========================= */
const navbar = document.querySelector(".navbar");
if (navbar) {
    navbar.style.background = "#ffffff";
    navbar.style.backgroundColor = "#ffffff";
    navbar.style.opacity = "1";
    navbar.style.backdropFilter = "none";
    navbar.style.webkitBackdropFilter = "none";
    navbar.style.boxShadow = "0 5px 20px rgba(0,0,0,0.08)";
    navbar.style.transition = "none";
}

/* =========================
   Hero Card 3D Tilt Effect
========================= */
const heroCard = document.querySelector(".hero-card");
if (heroCard) {
    heroCard.addEventListener("mousemove", (e) => {
        let rect = heroCard.getBoundingClientRect();
        let x = e.clientX - rect.left;
        let y = e.clientY - rect.top;

        let rotateX = ((y - rect.height / 2) / 20) * -1;
        let rotateY = (x - rect.width / 2) / 20;

        heroCard.style.transform = `
            perspective(1000px)
            rotateX(${rotateX}deg)
            rotateY(${rotateY}deg)
            scale(1.05)
        `;
    });

    heroCard.addEventListener("mouseleave", () => {
        heroCard.style.transform = `
            perspective(1000px)
            rotateX(0deg)
            rotateY(0deg)
            scale(1)
        `;
    });
}

/* =========================
   Feature Scroll Reveal
========================= */
const features = document.querySelectorAll(".feature-card");
function reveal() {
    features.forEach((card) => {
        let top = card.getBoundingClientRect().top;
        let height = window.innerHeight;

        if (top < height - 120) {
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
        }
    });
}
window.addEventListener("scroll", reveal);
reveal();

/* =========================
   Counter Animation
========================= */
const counters = document.querySelectorAll(".counter");
counters.forEach(counter => {
    let target = parseInt(counter.dataset.target);
    let count = 0;

    if (!isNaN(target)) {
        let timer = setInterval(() => {
            count += Math.ceil(target / 80);
            if (count >= target) {
                count = target;
                clearInterval(timer);
            }
            counter.innerText = count;
        }, 30);
    }
});

/* =========================
   Button Ripple Effect
========================= */
const buttons = document.querySelectorAll(".btn");
buttons.forEach(button => {
    button.addEventListener("click", function (e) {
        let ripple = document.createElement("span");
        ripple.className = "ripple";
        let rect = this.getBoundingClientRect();
        ripple.style.left = `${e.clientX - rect.left}px`;
        ripple.style.top = `${e.clientY - rect.top}px`;

        this.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

/* =========================
   Smooth Scroll
========================= */
document.querySelectorAll("a[href^='#']").forEach(link => {
    link.addEventListener("click", (e) => {
        const targetId = link.getAttribute("href");
        if (targetId !== "#" && targetId !== "") {
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({ behavior: "smooth" });
            }
        }
    });
});

/* =========================
   LOGIN CARD 3D EFFECT
========================= */
const authCard = document.querySelector(".auth-card");
if (authCard) {
    authCard.addEventListener("mousemove", (e) => {
        const rect = authCard.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        let rotateX = ((y - rect.height / 2) / 25) * -1;
        let rotateY = (x - rect.width / 2) / 25;

        authCard.style.transform = `
            perspective(1000px)
            rotateX(${rotateX}deg)
            rotateY(${rotateY}deg)
            scale(1.01)
        `;
    });

    authCard.addEventListener("mouseleave", () => {
        authCard.style.transform = `
            perspective(1000px)
            rotateX(0deg)
            rotateY(0deg)
            scale(1)
        `;
    });
}

/* =========================
   INPUT FOCUS ANIMATION
========================= */
const inputs = document.querySelectorAll(".input-group-custom input");
inputs.forEach(input => {
    input.addEventListener("focus", () => {
        if (input.parentElement) {
            input.parentElement.style.transform = "translateY(-3px)";
        }
    });

    input.addEventListener("blur", () => {
        if (input.parentElement) {
            input.parentElement.style.transform = "translateY(0)";
        }
    });
});

/* =========================
   PASSWORD SHOW / HIDE
========================= */
const toggleButtons = document.querySelectorAll(".toggle-password");
toggleButtons.forEach(button => {
    button.addEventListener("click", (e) => {
        e.preventDefault();
        const wrapper = button.closest(".password-wrapper") || button.parentElement;
        const input = wrapper.querySelector("input");

        if (input) {
            if (input.type === "password") {
                input.type = "text";
                button.textContent = "🙈";
            } else {
                input.type = "password";
                button.textContent = "👁️";
            }
        }
    });
});

/* =========================
   LOGIN VALIDATION & RESET
========================= */
const loginForm = document.getElementById("loginForm");
if (loginForm) {
    const submitBtn = loginForm.querySelector('button[type="submit"]');

    loginForm.addEventListener("submit", function (e) {
        const emailInput = document.querySelector('input[name="email"]');
        const passwordInput = document.querySelector('input[name="password"]');
        const message = document.getElementById("loginMessage");

        if (!emailInput || !passwordInput) return;

        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();

        if (message) message.innerHTML = "";

        if (email === "") {
            e.preventDefault();
            if (message) message.innerHTML = `<div class="error-message" style="color: #ff4d4d; margin-bottom: 10px;">Please enter your email.</div>`;
            return;
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            e.preventDefault();
            if (message) message.innerHTML = `<div class="error-message" style="color: #ff4d4d; margin-bottom: 10px;">Please enter a valid email address.</div>`;
            return;
        }

        if (password === "") {
            e.preventDefault();
            if (message) message.innerHTML = `<div class="error-message" style="color: #ff4d4d; margin-bottom: 10px;">Please enter your password.</div>`;
            return;
        }

        if (password.length < 6) {
            e.preventDefault();
            if (message) message.innerHTML = `<div class="error-message" style="color: #ff4d4d; margin-bottom: 10px;">Password must be at least 6 characters.</div>`;
            return;
        }

        if (submitBtn) {
            submitBtn.innerText = "Logging in...";
            submitBtn.disabled = true;
        }
    });

    window.addEventListener("pageshow", function () {
        if (submitBtn) {
            submitBtn.innerText = "Login";
            submitBtn.disabled = false;
        }
    });
}

/* =========================
   REGISTER VALIDATION
========================= */
const registerForm = document.getElementById("registerForm");
if (registerForm) {
    registerForm.addEventListener("submit", (e) => {
        const nameInput = document.querySelector('input[name="name"]');
        const emailInput = document.querySelector('input[name="email"]');
        const passwordInput = document.querySelector('input[name="password"]');
        const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');
        const message = document.getElementById("registerMessage");

        const name = nameInput ? nameInput.value.trim() : "";
        const email = emailInput ? emailInput.value.trim() : "";
        const password = passwordInput ? passwordInput.value.trim() : "";
        const confirmPassword = confirmPasswordInput ? confirmPasswordInput.value.trim() : "";

        if (message) message.innerHTML = "";

        if (name === "") {
            e.preventDefault();
            if (message) message.innerHTML = `<div class="register-error" style="color: #ff4d4d; margin-bottom: 10px;">Please enter your name.</div>`;
            return;
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            e.preventDefault();
            if (message) message.innerHTML = `<div class="register-error" style="color: #ff4d4d; margin-bottom: 10px;">Enter a valid email.</div>`;
            return;
        }

        if (password.length < 6) {
            e.preventDefault();
            if (message) message.innerHTML = `<div class="register-error" style="color: #ff4d4d; margin-bottom: 10px;">Password must be at least 6 characters.</div>`;
            return;
        }

        if (password !== confirmPassword) {
            e.preventDefault();
            if (message) message.innerHTML = `<div class="register-error" style="color: #ff4d4d; margin-bottom: 10px;">Passwords do not match.</div>`;
            return;
        }
    });
}