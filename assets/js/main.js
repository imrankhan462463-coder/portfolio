/* ==========================================================================
   IMRAN — PORTFOLIO
   Main interactions
   --------------------------------------------------------------------------
   01. Utilities & preferences
   02. Editable profile hydration
   03. Preloader
   04. Header, scroll progress & active section
   05. Mobile navigation
   06. Reveal on scroll
   07. Typing effect
   08. Counters
   09. Subtle 3D tilt
   10. Projects: render & filter
   11. Back to top
   12. Toast
   13. Contact form
   14. Small enhancements
   ========================================================================== */
(function () {
  "use strict";

  /* ======================================================================
     01. UTILITIES & PREFERENCES
     ====================================================================== */
  const $ = (selector, scope = document) => scope.querySelector(selector);
  const $$ = (selector, scope = document) => Array.from(scope.querySelectorAll(selector));
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const finePointer = window.matchMedia("(pointer: fine)");
  const mobileBreakpoint = window.matchMedia("(max-width: 1023px)");
  const hasIO = "IntersectionObserver" in window;

  const DEFAULT_PROFILE = {
    full_name: "MD Imran Hosan",
    logo_text: "IMRAN.DEV",
    title: "Web Designer & WordPress Developer",
    email: "imransikdar462@gmail.com",
    phone: "+8801623331050",
    whatsapp_url: "https://wa.me/8801623331050",
    location: "Mirpur 10, Dhaka, Bangladesh",
    location_timezone: "Asia/Dhaka",
    linkedin: "https://www.linkedin.com/in/imran-sikdar-02183733b",
    github: "https://github.com/imrankhan462463-coder",
    github_username: "imrankhan462463-coder",
    profile_image: "assets/images/imran.jpg",
    resume: "assets/files/imran-cv.pdf",
    availability: "Available for new projects",
    response_time: "Replies within 24 hours",
    hero_heading: "Building Digital Experiences That Inspire.",
    hero_description:
      "I am MD Imran Hosan, a Web Designer and WordPress Developer passionate about creating modern, responsive, user-friendly, and visually stunning websites that help businesses grow online.",
    bio_short:
      "I'm MD Imran Hosan, a Web Designer and WordPress Developer based in Mirpur 10, Dhaka. I design and build modern, responsive websites and custom WordPress solutions that are fast, easy to manage, and focused on real business goals.",
  };

  const rafThrottle = (callback) => {
    let ticking = false;
    return function throttled(...args) {
      if (ticking) return;
      ticking = true;
      window.requestAnimationFrame(() => {
        callback.apply(this, args);
        ticking = false;
      });
    };
  };

  const createEl = (tag, className, text) => {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (typeof text === "string") node.textContent = text;
    return node;
  };

  const isExternal = (url) => /^https?:\/\//i.test(String(url || ""));

  const applyLink = (link, url) => {
    if (!link) return;
    if (!url) {
      link.hidden = true;
      return;
    }
    link.hidden = false;
    link.setAttribute("href", url);
    if (isExternal(url)) {
      link.setAttribute("target", "_blank");
      link.setAttribute("rel", "noopener noreferrer");
    }
  };

  /* ======================================================================
     02. EDITABLE PROFILE HYDRATION
     ====================================================================== */
  const profileLinks = {
    email: (p) => (p.email ? "mailto:" + p.email : ""),
    phone: (p) => (p.phone ? "tel:" + String(p.phone).replace(/[^\d+]/g, "") : ""),
    whatsapp: (p) =>
      p.whatsapp_url || (p.whatsapp ? "https://wa.me/" + String(p.whatsapp).replace(/\D/g, "") : ""),
    linkedin: (p) => p.linkedin || "",
    github: (p) => p.github || "",
    facebook: (p) => p.facebook || "",
    instagram: (p) => p.instagram || "",
    resume: (p) => p.resume || "",
  };

  const renderHeroHeading = (element, heading) => {
    if (!element || typeof heading !== "string") return;
    const words = heading.trim().split(/\s+/).filter(Boolean);
    if (!words.length) return;

    const accent = words.pop();
    const middle = Math.ceil(words.length / 2);
    const lines = [words.slice(0, middle).join(" "), words.slice(middle).join(" ")].filter(Boolean);

    element.textContent = "";
    lines.forEach((line) => element.appendChild(createEl("span", "hero__line", line)));

    const accentLine = createEl("span", "hero__line hero__line--accent");
    const em = createEl("em", "serif", accent);
    accentLine.appendChild(em);
    element.appendChild(accentLine);
  };

  const applyProfile = (profile) => {
    const data = Object.assign({}, DEFAULT_PROFILE, profile || {});

    $$("[data-profile]").forEach((node) => {
      const key = node.getAttribute("data-profile");
      const value = data[key];
      if (typeof value === "string" && value.trim() !== "") {
        node.textContent = value;
      }
    });

    $$("[data-profile-link]").forEach((link) => {
      const key = link.getAttribute("data-profile-link");
      const resolver = profileLinks[key];
      if (resolver) applyLink(link, resolver(data));
    });

    $$("[data-profile-image]").forEach((image) => {
      if (data.profile_image) image.setAttribute("src", data.profile_image);
    });

    $$("[data-profile-hero]").forEach((element) => {
      renderHeroHeading(element, data.hero_heading);
    });

    window.portfolioProfile = data;
    document.dispatchEvent(new CustomEvent("portfolio:profile-ready", { detail: data }));
  };

  const embeddedData = window.auroraPortfolio || null;

  const loadProfile = async () => {
    try {
      const response = await fetch("data/profile.json", {
        cache: "no-store",
        headers: { Accept: "application/json" },
      });
      if (!response.ok) throw new Error("Profile request failed");
      const data = await response.json();
      applyProfile(data);
    } catch (error) {
      // The HTML already contains the same real data as a fallback.
      applyProfile(DEFAULT_PROFILE);
    }
  };

  if (embeddedData && embeddedData.profile) {
    applyProfile(embeddedData.profile);
  } else {
    loadProfile();
  }

  /* ======================================================================
     03. PRELOADER
     ====================================================================== */
  const preloader = $("#preloader");
  let preloaderHidden = false;

  const hidePreloader = () => {
    if (!preloader || preloaderHidden) return;
    preloaderHidden = true;
    preloader.classList.add("is-hidden");
    window.setTimeout(() => preloader.remove(), 700);
  };

  if (preloader) {
    if (reduceMotion.matches) {
      hidePreloader();
    } else {
      window.addEventListener("load", () => window.setTimeout(hidePreloader, 450), { once: true });
      // Safety net if an external resource hangs.
      window.setTimeout(hidePreloader, 2800);
    }
  }

  /* ======================================================================
     04. HEADER, SCROLL PROGRESS & ACTIVE SECTION
     ====================================================================== */
  const header = $("#site-header");
  const progressBar = $("#scroll-progress-bar");
  const root = document.documentElement;
  const backToTop = $("#back-to-top");
  const backToTopValue = $("#back-to-top-value");
  const ringLength = 132;
  const navLinks = $$(".nav-link");
  const spySections = navLinks
    .map((link) => {
      const id = link.getAttribute("href");
      const section = id && id.startsWith("#") ? $(id) : null;
      return section ? { link, section } : null;
    })
    .filter(Boolean);

  const onScroll = () => {
    const scrollY = window.scrollY || window.pageYOffset;

    if (header) header.classList.toggle("is-scrolled", scrollY > 20);

    const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
    const progress = maxScroll > 0 ? Math.min(scrollY / maxScroll, 1) : 0;
    if (progressBar) progressBar.style.setProperty("--scroll-progress", progress.toFixed(4));
    root.style.setProperty("--scroll-progress", progress.toFixed(4));

    // Active navigation item.
    const marker = scrollY + window.innerHeight * 0.32;
    let current = spySections[0];
    spySections.forEach((entry) => {
      if (entry.section.offsetTop <= marker) current = entry;
    });

    if (current) {
      navLinks.forEach((link) => {
        const active = link === current.link;
        link.classList.toggle("is-active", active);
        if (active) {
          link.setAttribute("aria-current", "location");
        } else {
          link.removeAttribute("aria-current");
        }
      });
    }

    updateBackToTop(scrollY, maxScroll);
  };

  window.addEventListener("scroll", rafThrottle(onScroll), { passive: true });
  window.addEventListener("resize", rafThrottle(onScroll), { passive: true });
  onScroll();

  /* ======================================================================
     05. MOBILE NAVIGATION
     ====================================================================== */
  const navToggle = $("#nav-toggle");
  const mobileMenu = $("#mobile-menu");
  const mobilePanel = mobileMenu ? $(".mobile-menu__panel", mobileMenu) : null;
  const mobileFocusableSelector =
    'a[href], button:not([disabled]), input, textarea, select, [tabindex]:not([tabindex="-1"])';
  let menuOpen = false;
  let lastFocused = null;

  const openMenu = () => {
    if (!mobileMenu || !navToggle || menuOpen) return;
    menuOpen = true;
    lastFocused = document.activeElement;
    mobileMenu.classList.add("is-open");
    mobileMenu.setAttribute("aria-hidden", "false");
    navToggle.classList.add("is-active");
    navToggle.setAttribute("aria-expanded", "true");
    navToggle.setAttribute("aria-label", "Close menu");
    document.body.classList.add("menu-open");
    const focusables = mobilePanel ? $$(mobileFocusableSelector, mobilePanel) : [];
    window.setTimeout(() => {
      if (focusables.length) focusables[0].focus();
    }, 60);
  };

  const closeMenu = () => {
    if (!mobileMenu || !navToggle || !menuOpen) return;
    menuOpen = false;
    mobileMenu.classList.remove("is-open");
    mobileMenu.setAttribute("aria-hidden", "true");
    navToggle.classList.remove("is-active");
    navToggle.setAttribute("aria-expanded", "false");
    navToggle.setAttribute("aria-label", "Open menu");
    document.body.classList.remove("menu-open");
    if (lastFocused && typeof lastFocused.focus === "function") lastFocused.focus();
  };

  if (navToggle) {
    navToggle.addEventListener("click", () => {
      if (menuOpen) closeMenu();
      else openMenu();
    });
  }

  if (mobileMenu) {
    $$("[data-menu-close]", mobileMenu).forEach((el) => {
      el.addEventListener("click", closeMenu);
    });

    $$("a[href]", mobileMenu).forEach((link) => {
      link.addEventListener("click", closeMenu);
    });

    document.addEventListener("keydown", (event) => {
      if (!menuOpen) return;
      if (event.key === "Escape") {
        event.preventDefault();
        closeMenu();
      }
      if (event.key === "Tab" && mobilePanel) {
        const focusables = $$(mobileFocusableSelector, mobilePanel).filter(
          (el) => el.offsetParent !== null
        );
        if (!focusables.length) return;
        const first = focusables[0];
        const last = focusables[focusables.length - 1];
        if (event.shiftKey && document.activeElement === first) {
          event.preventDefault();
          last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
          event.preventDefault();
          first.focus();
        }
      }
    });

    mobileBreakpoint.addEventListener("change", (event) => {
      if (!event.matches) closeMenu();
    });
  }

  /* ======================================================================
     06. REVEAL ON SCROLL
     ====================================================================== */
  const revealElements = $$("[data-reveal]");

  revealElements.forEach((el) => {
    const delay = Number(el.getAttribute("data-reveal-delay") || 0);
    if (delay) el.style.setProperty("--reveal-delay", `${delay}ms`);
  });

  if (reduceMotion.matches || !hasIO) {
    revealElements.forEach((el) => el.classList.add("is-revealed"));
  } else {
    const revealObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-revealed");
            revealObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1, rootMargin: "0px 0px -6% 0px" }
    );
    revealElements.forEach((el) => revealObserver.observe(el));
  }

  /* ======================================================================
     07. TYPING EFFECT
     ====================================================================== */
  const typedEl = $("#typed-role");
  if (typedEl) {
    let roles = [];
    try {
      roles = JSON.parse(typedEl.getAttribute("data-roles") || "[]");
    } catch (error) {
      roles = [];
    }
    roles = roles.filter((role) => typeof role === "string" && role.trim().length);

    if (reduceMotion.matches || roles.length === 0) {
      if (roles.length) typedEl.textContent = roles[0];
    } else {
      let roleIndex = 0;
      let charIndex = 0;
      let deleting = false;
      let timer = null;

      const tick = () => {
        const role = roles[roleIndex];
        const visible = role.slice(0, charIndex);
        typedEl.textContent = visible;

        let delay = deleting ? 38 : 62;

        if (!deleting && charIndex === role.length) {
          delay = 1750;
          deleting = true;
        } else if (deleting && charIndex === 0) {
          deleting = false;
          roleIndex = (roleIndex + 1) % roles.length;
          delay = 380;
        }

        charIndex += deleting ? -1 : 1;
        if (charIndex < 0) charIndex = 0;
        if (charIndex > role.length) charIndex = role.length;

        timer = window.setTimeout(tick, delay);
      };

      const start = () => {
        if (timer) window.clearTimeout(timer);
        tick();
      };

      document.addEventListener("visibilitychange", () => {
        if (document.hidden) {
          window.clearTimeout(timer);
        } else {
          start();
        }
      });

      // Let the page settle before the first keystroke.
      window.setTimeout(start, 900);
    }
  }

  /* ======================================================================
     08. COUNTERS
     ====================================================================== */
  const counters = $$(".counter");
  const animateCounter = (el) => {
    const target = Number(el.getAttribute("data-target") || 0);
    if (reduceMotion.matches) {
      el.textContent = String(target);
      return;
    }
    const duration = 1500;
    const start = performance.now();

    const step = (now) => {
      const elapsed = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - elapsed, 3);
      el.textContent = String(Math.round(target * eased));
      if (elapsed < 1) window.requestAnimationFrame(step);
    };
    window.requestAnimationFrame(step);
  };

  if (counters.length) {
    if (reduceMotion.matches || !hasIO) {
      counters.forEach((el) => (el.textContent = el.getAttribute("data-target") || "0"));
    } else {
      const counterObserver = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              animateCounter(entry.target);
              counterObserver.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.6 }
      );
      counters.forEach((el) => counterObserver.observe(el));
    }
  }

  /* ======================================================================
     09. SUBTLE 3D TILT
     ====================================================================== */
  const canTilt = () => finePointer.matches && !reduceMotion.matches && !mobileBreakpoint.matches;

  const initTilt = (element, maxTilt = 4) => {
    let frame = null;

    const handleMove = (event) => {
      if (!canTilt()) return;
      const rect = element.getBoundingClientRect();
      const x = (event.clientX - rect.left) / rect.width - 0.5;
      const y = (event.clientY - rect.top) / rect.height - 0.5;
      if (frame) window.cancelAnimationFrame(frame);
      frame = window.requestAnimationFrame(() => {
        element.classList.add("is-tilting");
        element.style.transform = `perspective(1000px) rotateX(${(-y * maxTilt).toFixed(
          2
        )}deg) rotateY(${(x * maxTilt).toFixed(2)}deg)`;
      });
    };

    const handleLeave = () => {
      if (frame) window.cancelAnimationFrame(frame);
      element.classList.remove("is-tilting");
      element.style.transform = "";
    };

    element.addEventListener("pointermove", handleMove);
    element.addEventListener("pointerleave", handleLeave);
    element.addEventListener("blur", handleLeave);
  };

  const heroScene = $("#hero-scene");
  if (heroScene && canTilt()) initTilt(heroScene, 5);

  const enableCardTilt = () => {
    if (!canTilt()) return;
    $$(".project-card").forEach((card) => initTilt(card, 2.2));
  };

  enableCardTilt();

  /* ======================================================================
     10. PROJECTS: RENDER & FILTER
     ====================================================================== */
  const projectsGrid = $("#projects-grid");
  const filterButtons = $$(".filter-btn");
  let activeFilter = "all";
  let projectData = [];

  const filterStatus = createEl("p", "sr-only");
  filterStatus.setAttribute("aria-live", "polite");
  if (projectsGrid) projectsGrid.after(filterStatus);

  const projectCard = (project) => {
    const card = createEl("article", "project-card");
    card.setAttribute("data-category", project.category || "concept");

    const media = createEl("div", "project-card__media");
    const image = document.createElement("img");
    image.src = project.image || "";
    image.alt = project.name
      ? `${project.name} project preview`
      : "Portfolio project preview";
    image.width = 1200;
    image.height = 800;
    image.loading = "lazy";
    image.decoding = "async";
    media.appendChild(image);
    media.appendChild(createEl("span", "project-card__shade"));

    const chip = createEl("span", "project-card__chip", project.category_label || "Project");
    media.appendChild(chip);

    const open = createEl("span", "project-card__open");
    open.innerHTML = '<svg aria-hidden="true"><use href="#icon-arrow-up-right"></use></svg>';
    media.appendChild(open);
    card.appendChild(media);

    const body = createEl("div", "project-card__body");
    const meta = createEl("div", "project-card__meta");
    meta.appendChild(createEl("span", "", project.category_detail || project.category_label || "Project"));
    meta.appendChild(createEl("span", "", project.year || ""));
    body.appendChild(meta);

    body.appendChild(createEl("h3", "", project.name || "Untitled project"));
    body.appendChild(createEl("p", "", project.description || ""));

    if (Array.isArray(project.tech) && project.tech.length) {
      const tech = createEl("ul", "tech-list");
      tech.setAttribute("aria-label", "Technologies used");
      project.tech.forEach((item) => tech.appendChild(createEl("li", "", String(item))));
      body.appendChild(tech);
    }

    const actions = createEl("div", "project-card__actions");
    if (project.demo_url) {
      const demo = createEl("a", "btn btn--primary btn--sm");
      demo.href = project.demo_url;
      if (isExternal(project.demo_url)) {
        demo.target = "_blank";
        demo.rel = "noopener noreferrer";
      }
      demo.appendChild(createEl("span", "", "Live demo"));
      demo.insertAdjacentHTML("beforeend", '<svg class="btn__icon" aria-hidden="true"><use href="#icon-arrow-up-right"></use></svg>');
      actions.appendChild(demo);
    }
    if (project.source_url) {
      const source = createEl("a", "btn btn--ghost btn--sm");
      source.href = project.source_url;
      if (isExternal(project.source_url)) {
        source.target = "_blank";
        source.rel = "noopener noreferrer";
      }
      source.appendChild(createEl("span", "", "Source code"));
      source.insertAdjacentHTML("beforeend", '<svg class="btn__icon" aria-hidden="true"><use href="#icon-code"></use></svg>');
      actions.appendChild(source);
    }
    if (!actions.children.length) {
      const discuss = createEl("a", "btn btn--ghost btn--sm");
      discuss.href = "#contact";
      discuss.appendChild(createEl("span", "", "Discuss a project"));
      discuss.insertAdjacentHTML("beforeend", '<svg class="btn__icon" aria-hidden="true"><use href="#icon-arrow-up-right"></use></svg>');
      actions.appendChild(discuss);
    }
    body.appendChild(actions);
    card.appendChild(body);

    return card;
  };

  const filterProjects = (filter) => {
    activeFilter = filter || "all";
    const cards = $$(".project-card", projectsGrid || document);
    let visible = 0;

    cards.forEach((card) => {
      const matches = activeFilter === "all" || card.getAttribute("data-category") === activeFilter;
      card.classList.toggle("is-filtered-out", !matches);
      if (matches) visible += 1;
    });

    if (filterStatus) {
      filterStatus.textContent = `Showing ${visible} project${visible === 1 ? "" : "s"}.`;
    }
  };

  const updateFilterCounts = (projects) => {
    const counts = { all: projects.length };
    projects.forEach((project) => {
      const key = project.category || "concept";
      counts[key] = (counts[key] || 0) + 1;
    });

    filterButtons.forEach((button) => {
      const key = button.getAttribute("data-filter") || "all";
      const badge = $("span", button);
      if (badge) badge.textContent = String(counts[key] || 0).padStart(2, "0");
      if (key !== "all" && !counts[key]) {
        button.hidden = true;
      } else {
        button.hidden = false;
      }
    });
  };

  const renderProjects = (projects) => {
    if (!projectsGrid || !Array.isArray(projects) || !projects.length) return;
    projectsGrid.textContent = "";
    projects.forEach((project) => projectsGrid.appendChild(projectCard(project)));
    updateFilterCounts(projects);
    filterProjects(activeFilter);
    enableCardTilt();
    onScroll();
  };

  const loadProjects = async () => {
    try {
      const response = await fetch("data/projects.json", {
        cache: "no-store",
        headers: { Accept: "application/json" },
      });
      if (!response.ok) throw new Error("Projects request failed");
      const data = await response.json();
      const projects = Array.isArray(data.projects) ? data.projects : [];
      projectData = projects;
      renderProjects(projects);
    } catch (error) {
      // Keep the static, accessible fallback cards already in the HTML.
      filterProjects(activeFilter);
    }
  };

  filterButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const filter = button.getAttribute("data-filter") || "all";
      filterButtons.forEach((btn) => {
        const active = btn === button;
        btn.classList.toggle("is-active", active);
        btn.setAttribute("aria-pressed", active ? "true" : "false");
      });
      filterProjects(filter);
    });
  });

  filterProjects(activeFilter);

  if (embeddedData && Array.isArray(embeddedData.projects) && embeddedData.projects.length) {
    projectData = embeddedData.projects;
    renderProjects(projectData);
  } else {
    loadProjects();
  }

  /* ======================================================================
     11. BACK TO TOP
     ====================================================================== */
  function updateBackToTop(scrollY, maxScroll) {
    if (!backToTop) return;
    backToTop.classList.toggle("is-visible", scrollY > 700);
    if (backToTopValue) {
      const progress = maxScroll > 0 ? Math.min(scrollY / maxScroll, 1) : 0;
      backToTopValue.style.strokeDashoffset = String(ringLength - ringLength * progress);
    }
  }

  if (backToTop) {
    backToTop.addEventListener("click", () => {
      window.scrollTo({
        top: 0,
        behavior: reduceMotion.matches ? "auto" : "smooth",
      });
    });
  }

  /* ======================================================================
     12. TOAST
     ====================================================================== */
  const toast = $("#toast");
  const toastMessage = toast ? $(".toast__message", toast) : null;
  let toastTimer = null;

  const showToast = (message, type = "success") => {
    if (!toast || !toastMessage) return;
    window.clearTimeout(toastTimer);
    toastMessage.textContent = message;
    toast.classList.toggle("is-error", type === "error");
    toast.classList.add("is-visible");
    toast.setAttribute("aria-hidden", "false");
    toastTimer = window.setTimeout(() => {
      toast.classList.remove("is-visible");
      toast.setAttribute("aria-hidden", "true");
    }, 4200);
  };

  /* ======================================================================
     13. CONTACT FORM
     ====================================================================== */
  const form = $("#contact-form");
  if (form) {
    const submitBtn = $(".contact-form__submit", form);
    const statusEl = $("#form-status", form);
    const csrfEl = $("#cf-csrf", form);
    const fields = {
      name: $("#cf-name", form),
      email: $("#cf-email", form),
      phone: $("#cf-phone", form),
      location: $("#cf-location", form),
      project_type: $("#cf-project-type", form),
      budget: $("#cf-budget", form),
      subject: $("#cf-subject", form),
      message: $("#cf-message", form),
      website: $("#cf-website", form),
    };
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    const phonePattern = /^\+?[0-9\s().-]{7,24}$/;

    const setFieldState = (input, message) => {
      if (!input) return false;
      const field = input.closest(".field");
      const errorEl = field ? $(".field__error", field) : null;
      if (!field) return false;
      if (message) {
        field.classList.add("is-invalid");
        input.setAttribute("aria-invalid", "true");
        if (errorEl) errorEl.textContent = message;
        return false;
      }
      field.classList.remove("is-invalid");
      input.removeAttribute("aria-invalid");
      if (errorEl) errorEl.textContent = "";
      return true;
    };

    const validate = (input) => {
      if (!input) return true;

      if (input === fields.name) {
        const value = input.value.trim();
        if (!value) return setFieldState(input, "Please tell me your name.");
        if (value.length < 2) return setFieldState(input, "That name looks a little short.");
        if (value.length > 100) return setFieldState(input, "Please keep your name under 100 characters.");
      }

      if (input === fields.email) {
        const value = input.value.trim();
        if (!value) return setFieldState(input, "Please add an email address.");
        if (value.length > 190 || !emailPattern.test(value)) {
          return setFieldState(input, "Please enter a valid email address.");
        }
      }

      if (input === fields.phone) {
        const value = input.value.trim();
        if (!value) return setFieldState(input, "Please add a phone number.");
        if (!phonePattern.test(value)) {
          return setFieldState(input, "Use digits, spaces, +, -, ( ) or . only.");
        }
      }

      if (input === fields.location) {
        if (input.value.trim().length > 120) {
          return setFieldState(input, "Please keep the location under 120 characters.");
        }
      }

      if (input === fields.subject) {
        if (input.value.trim().length > 180) {
          return setFieldState(input, "Please keep the subject under 180 characters.");
        }
      }

      if (input === fields.project_type) {
        if (!input.value) return setFieldState(input, "Please choose a project type.");
      }

      if (input === fields.message) {
        const value = input.value.trim();
        if (!value) return setFieldState(input, "Please add a message.");
        if (value.length < 10) return setFieldState(input, "A little more detail, please (10+ characters).");
        if (value.length > 6000) return setFieldState(input, "Please keep the message under 6,000 characters.");
      }

      return setFieldState(input, "");
    };

    Object.values(fields).forEach((input) => {
      if (!input) return;
      const eventName = input.tagName === "SELECT" ? "change" : "input";
      input.addEventListener("blur", () => validate(input));
      input.addEventListener(eventName, () => {
        const field = input.closest(".field");
        if (field && field.classList.contains("is-invalid")) validate(input);
      });
    });

    const setStatus = (message, type) => {
      if (!statusEl) return;
      statusEl.textContent = message;
      statusEl.classList.remove("is-success", "is-error");
      if (type) statusEl.classList.add(`is-${type}`);
    };

    const setLoading = (loading) => {
      if (!submitBtn) return;
      submitBtn.classList.toggle("is-loading", loading);
      submitBtn.disabled = loading;
      submitBtn.setAttribute("aria-busy", loading ? "true" : "false");
      const label = $(".btn__label", submitBtn);
      if (label) label.textContent = loading ? "Sending…" : "Send Message";
    };

    const profileEmail = () =>
      (window.portfolioProfile && window.portfolioProfile.email) || DEFAULT_PROFILE.email;

    const loadToken = async () => {
      if (!csrfEl) return;
      try {
        const response = await fetch("contact-token.php", {
          cache: "no-store",
          headers: { Accept: "application/json" },
        });
        if (!response.ok) return;
        const data = await response.json();
        if (data && data.token) csrfEl.value = data.token;
      } catch (error) {
        // The endpoint only enforces the token when a session token exists.
      }
    };

    if (!embeddedData) loadToken();

    const emailFallback = () => {
      const email = profileEmail();
      if (!statusEl) return;
      statusEl.textContent = "This form needs a PHP server. Email me directly at ";
      const link = document.createElement("a");
      link.href = "mailto:" + email;
      link.textContent = email;
      link.style.color = "#c4b5fd";
      link.style.textDecoration = "underline";
      statusEl.appendChild(link);
      statusEl.append(" and I'll reply within a day.");
    };

    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      setStatus("", null);

      // Honeypot: quietly reject bots without giving them feedback.
      if (fields.website && fields.website.value.trim()) {
        setStatus("Thanks — your message has been sent.", "success");
        form.reset();
        return;
      }

      const required = [fields.name, fields.email, fields.phone, fields.project_type, fields.message];
      const optional = [fields.location, fields.subject, fields.budget];
      required.concat(optional).forEach(validate);

      const invalid = required.find(
        (input) => input && input.closest(".field")?.classList.contains("is-invalid")
      );

      if (invalid) {
        invalid.focus();
        setStatus("Please check the highlighted fields and try again.", "error");
        return;
      }

      setLoading(true);
      const formData = new FormData(form);

      try {
        const response = await fetch(form.action, {
          method: "POST",
          body: formData,
          headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
        });

        let data = null;
        try {
          data = await response.json();
        } catch (parseError) {
          data = null;
        }

        if (response.ok && data && data.success) {
          form.reset();
          if (csrfEl) csrfEl.value = "";
          setStatus(data.message || "Message sent — thank you. I'll be in touch shortly.", "success");
          showToast("Message sent. I'll reply within one business day.", "success");
          Object.values(fields).forEach((input) => input && setFieldState(input, ""));
          if (!embeddedData) loadToken();
        } else if (data && data.message) {
          setStatus(data.message, "error");
          showToast("Your message couldn't be sent. Please try again.", "error");
        } else {
          throw new Error("Unexpected response");
        }
      } catch (error) {
        const onFileProtocol = window.location.protocol === "file:";
        if (onFileProtocol) {
          emailFallback();
        } else {
          setStatus(
            `Something went wrong. Please email ${profileEmail()} directly — I'll reply quickly.`,
            "error"
          );
        }
        showToast("The message couldn't be sent. Please try again.", "error");
      } finally {
        setLoading(false);
      }
    });

    // Service links prefill the contact form subject and project type.
    const projectTypeMap = {
      "Website Design": "Website Design",
      "WordPress Website Development": "WordPress Website Development",
      "Custom WordPress Theme": "Custom WordPress Theme",
      "WordPress Plugin Development": "Custom WordPress Plugin",
      "Responsive Website Development": "Responsive Website Design",
      "Website Redesign": "Website Redesign & Maintenance",
      "E-commerce Website": "E-commerce Website",
      "Business Website": "Business Website",
    };

    $$("[data-subject]").forEach((link) => {
      link.addEventListener("click", () => {
        const subject = link.getAttribute("data-subject") || "";
        if (fields.subject) fields.subject.value = subject;
        if (fields.project_type && projectTypeMap[subject]) {
          fields.project_type.value = projectTypeMap[subject];
          setFieldState(fields.project_type, "");
        }
        window.setTimeout(() => {
          if (fields.subject) fields.subject.focus({ preventScroll: true });
        }, 650);
      });
    });

    // Messages from the no-JavaScript fallback (contact.php redirects back).
    const params = new URLSearchParams(window.location.search);
    if (params.get("sent") === "1") {
      setStatus(
        "Thanks for reaching out — your message has been sent. I'll reply within one business day.",
        "success"
      );
      showToast("Message sent — thank you.", "success");
      window.history.replaceState({}, "", `${window.location.pathname}#contact`);
    } else if (params.has("error")) {
      const reason =
        params.get("error") === "server"
          ? `The message could not be sent right now. Please email ${profileEmail()} directly.`
          : "Some details were missing. Please check the form and try again.";
      setStatus(reason, "error");
      window.history.replaceState({}, "", `${window.location.pathname}#contact`);
    }
  }

  /* ======================================================================
     14. SMALL ENHANCEMENTS
     ====================================================================== */
  const yearEl = $("#year");
  if (yearEl) yearEl.textContent = String(new Date().getFullYear());

  // External links get safe rel attributes if they are ever added without them.
  $$('a[target="_blank"]').forEach((link) => {
    const rel = new Set((link.getAttribute("rel") || "").split(/\s+/).filter(Boolean));
    rel.add("noopener");
    rel.add("noreferrer");
    link.setAttribute("rel", Array.from(rel).join(" "));
  });

  // Keep the footer demos link pointing at an actual demo.
  $$('a[href="demo.html"]').forEach((link) => {
    if (!link.getAttribute("href").includes("?")) link.setAttribute("href", "demo.html?id=lumen");
  });
})();
