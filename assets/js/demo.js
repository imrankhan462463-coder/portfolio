/* ==========================================================================
   IMRAN — CONCEPT DEMO RUNTIME
   Builds the live concept previews and source-code view from
   window.AURORA_PROJECTS. All copy is defined in projects-data.js.
   ========================================================================== */
(function () {
  "use strict";

  var projects = window.AURORA_PROJECTS;
  if (!projects) {
    return;
  }

  var order = Object.keys(projects);
  if (!order.length) {
    return;
  }

  var app = document.getElementById("demo-app");
  var frame = document.getElementById("demo-frame");
  var projectSelect = document.getElementById("project-select");
  var barProject = document.getElementById("bar-project");
  var barCategory = document.getElementById("bar-category");
  var demoUrl = document.getElementById("demo-url");
  var tabs = Array.prototype.slice.call(document.querySelectorAll("[data-view]"));
  var panels = {
    demo: document.getElementById("demo-panel"),
    code: document.getElementById("code-panel")
  };
  var codeFilename = document.getElementById("code-filename");
  var codeLanguage = document.getElementById("code-language");
  var codeContent = document.getElementById("code-content");
  var codeBody = codeContent ? codeContent.closest(".code-shell__body") : null;
  var copyButton = document.getElementById("copy-code");
  var toast = document.getElementById("demo-toast");
  var toastText = document.getElementById("demo-toast-text");
  var status = document.getElementById("demo-status");
  var detailsName = document.getElementById("details-name");
  var detailsYear = document.getElementById("details-year");
  var detailsText = document.getElementById("details-text");
  var detailsCategory = document.getElementById("details-category");
  var detailsFocus = document.getElementById("details-focus");
  var detailsStack = document.getElementById("details-stack");
  var pagerPrev = document.getElementById("pager-prev");
  var pagerNext = document.getElementById("pager-next");

  if (!app || !frame || !projectSelect) {
    return;
  }

  var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  var currentId = order[0];
  var currentView = "demo";
  var toastTimer = 0;
  var copyTimer = 0;

  var icon = {
    arrow: '<svg aria-hidden="true"><use href="#icon-arrow-right"></use></svg>',
    arrowUp: '<svg aria-hidden="true"><use href="#icon-arrow-up-right"></use></svg>',
    check: '<svg aria-hidden="true"><use href="#icon-check"></use></svg>',
    quote: '<svg aria-hidden="true"><use href="#icon-quote"></use></svg>'
  };

  var languageLabels = {
    php: "PHP",
    js: "JavaScript",
    javascript: "JavaScript",
    css: "CSS",
    html: "HTML",
    json: "JSON",
    shell: "Shell"
  };

  /* ------------------------------------------------------------------------
     Helpers
     ------------------------------------------------------------------------ */
  function escapeHtml(value) {
    return String(value == null ? "" : value).replace(/[&<>"']/g, function (character) {
      return {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;"
      }[character];
    });
  }

  /**
   * Titles in projects-data.js intentionally contain trusted <em> and <br>
   * tags for typographic emphasis. Everything else is escaped.
   */
  function rich(value) {
    return escapeHtml(value)
      .replace(/&lt;(\/?)(em|br)&gt;/gi, function (match, close, tag) {
        return "<" + close + tag.toLowerCase() + ">";
      });
  }

  function attr(value) {
    return escapeHtml(value);
  }

  function setText(node, value) {
    if (node) {
      node.textContent = value == null ? "" : String(value);
    }
  }

  function projectIndex(id) {
    return order.indexOf(id);
  }

  function neighbour(id, direction) {
    var index = projectIndex(id);
    return order[(index + direction + order.length) % order.length];
  }

  function scrollFrameTo(target, offset) {
    var top = target.getBoundingClientRect().top - frame.getBoundingClientRect().top + frame.scrollTop - (offset || 0);
    frame.scrollTo({
      top: Math.max(0, top),
      behavior: reduceMotion.matches ? "auto" : "smooth"
    });
  }

  function showToast(message) {
    if (!toast || !toastText) {
      return;
    }
    setText(toastText, message);
    toast.classList.add("is-visible");
    toast.setAttribute("aria-hidden", "false");
    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(function () {
      toast.classList.remove("is-visible");
      toast.setAttribute("aria-hidden", "true");
    }, 2600);
  }

  /* ------------------------------------------------------------------------
     Demo site template
     ------------------------------------------------------------------------ */
  function buildSite(project) {
    var theme = project.theme || {};
    var serif = theme.serif ? "true" : "false";

    var navLinks = (project.nav || [])
      .map(function (item) {
        return '<a href="' + attr(item.href) + '">' + escapeHtml(item.label) + "</a>";
      })
      .join("");

    var logos = (project.logos || [])
      .map(function (logo) {
        return '<span class="demo-logos__item">' + escapeHtml(logo) + "</span>";
      })
      .join("");

    var features = (project.features || [])
      .map(function (feature) {
        return (
          '<article class="demo-feature">' +
          '<span class="demo-feature__no">' + escapeHtml(feature.no) + "</span>" +
          "<h3>" + escapeHtml(feature.title) + "</h3>" +
          "<p>" + escapeHtml(feature.text) + "</p>" +
          "</article>"
        );
      })
      .join("");

    var points = (project.split && project.split.points ? project.split.points : [])
      .map(function (point) {
        return "<li>" + icon.check + "<span>" + escapeHtml(point) + "</span></li>";
      })
      .join("");

    var palette = [theme.accent, theme.accent2, theme.bg, theme.text]
      .filter(Boolean)
      .map(function (color) {
        return '<i style="background:' + attr(color) + '" aria-hidden="true"></i>';
      })
      .join("");

    var split = project.split || {};
    var quote = project.quote || {};
    var cta = project.cta || {};
    var stat = project.floatStat || {};

    var site = document.createElement("div");
    site.className = "demo-site";
    site.setAttribute("data-project", project.id);

    site.innerHTML =
      '<header class="demo-site__header">' +
      '<div class="demo-site__header-inner">' +
      '<a class="demo-site__brand" href="#hero">' + escapeHtml(project.brand) + "</a>" +
      '<nav class="demo-site__nav" aria-label="Concept demo navigation">' + navLinks + "</nav>" +
      '<a class="demo-site__header-cta" href="' + attr(cta.href || "index.html#contact") + '">Let\'s talk ' + icon.arrowUp + "</a>" +
      "</div>" +
      "</header>" +

      "<main>" +
      '<section class="demo-hero" id="hero">' +
      '<div class="demo-wrap demo-hero__grid">' +
      '<div class="demo-hero__copy">' +
      '<span class="demo-eyebrow">' + escapeHtml(project.eyebrow) + "</span>" +
      '<h1 class="demo-hero__title">' + rich(project.title) + "</h1>" +
      '<p class="demo-hero__subtitle">' + escapeHtml(project.subtitle) + "</p>" +
      '<div class="demo-buttons">' +
      '<a class="demo-btn demo-btn--primary" href="' + attr(project.primaryHref) + '">' + escapeHtml(project.primaryCta) + icon.arrow + "</a>" +
      '<a class="demo-btn demo-btn--ghost" href="' + attr(project.secondaryHref) + '">' + escapeHtml(project.secondaryCta) + "</a>" +
      "</div>" +
      "</div>" +
      '<div class="demo-hero__visual">' +
      '<span class="demo-hero__glow" aria-hidden="true"></span>' +
      '<figure class="demo-shot">' +
      '<img src="' + attr(project.cover) + '" alt="' + attr(project.coverAlt) + '" width="1200" height="800" decoding="async">' +
      '<span class="demo-shot__tag">Concept preview</span>' +
      "</figure>" +
      '<div class="demo-stat">' +
      '<span class="demo-stat__value">' + escapeHtml(stat.value) + "</span>" +
      '<span class="demo-stat__label">' + escapeHtml(stat.label) + "</span>" +
      '<span class="demo-stat__meta">' + escapeHtml(stat.meta) + "</span>" +
      "</div>" +
      "</div>" +
      "</div>" +
      "</section>" +

      '<div class="demo-logos">' +
      '<div class="demo-logos__inner">' +
      '<span class="demo-logos__label">Concept brand marks</span>' + logos +
      "</div>" +
      "</div>" +

      '<section class="demo-section demo-section--tinted" id="features">' +
      '<div class="demo-wrap">' +
      '<div class="demo-section__head">' +
      "<div>" +
      '<span class="demo-eyebrow">' + escapeHtml(project.featuresLabel) + "</span>" +
      '<h2 class="demo-section__title">' + rich(project.featuresTitle) + "</h2>" +
      "</div>" +
      '<p class="demo-section__text">' + escapeHtml(project.featuresText) + "</p>" +
      "</div>" +
      '<div class="demo-features">' + features + "</div>" +
      "</div>" +
      "</section>" +

      '<section class="demo-section" id="story">' +
      '<div class="demo-wrap demo-story">' +
      '<div class="demo-story__visual">' +
      '<span class="demo-story__label">' + escapeHtml(split.visualLabel) + "</span>" +
      '<span class="demo-story__word">' + escapeHtml(split.visualWord) + "</span>" +
      '<span class="demo-story__meta">' + escapeHtml(split.visualMeta) + "</span>" +
      "</div>" +
      "<div>" +
      '<span class="demo-eyebrow">' + escapeHtml(split.label) + "</span>" +
      '<h2 class="demo-story__title">' + rich(split.title) + "</h2>" +
      '<p class="demo-story__text">' + escapeHtml(split.text) + "</p>" +
      '<ul class="demo-points">' + points + "</ul>" +
      "</div>" +
      "</div>" +
      "</section>" +

      '<section class="demo-section demo-section--tinted" id="work">' +
      '<div class="demo-wrap">' +
      '<div class="demo-section__head">' +
      "<div>" +
      '<span class="demo-eyebrow">Selected details</span>' +
      '<h2 class="demo-section__title">A closer look at <em>' + escapeHtml(project.shortName) + "</em></h2>" +
      "</div>" +
      '<p class="demo-section__text">A few interface, typography and palette decisions from the concept build — shown at desktop scale and ready to be adapted.</p>' +
      "</div>" +
      '<div class="demo-work">' +
      '<figure class="demo-tile demo-tile--image">' +
      '<img src="' + attr(project.cover) + '" alt="' + attr(project.coverAlt) + '" loading="lazy" decoding="async">' +
      '<figcaption class="demo-tile__caption"><span>Landing page</span><strong>' + escapeHtml(project.name) + "</strong></figcaption>" +
      "</figure>" +
      '<div class="demo-tile demo-tile--type" data-serif="' + serif + '">' +
      '<span class="demo-type__sample" aria-hidden="true">Aa</span>' +
      '<span class="demo-type__meta"><span>' + (theme.serif ? "Instrument Serif + Inter" : "Sora + Inter") + "</span>" +
      '<span class="demo-palette" aria-label="Concept colour palette">' + palette + "</span></span>" +
      "</div>" +
      '<div class="demo-tile demo-tile--stat">' +
      '<span class="demo-stat__big">' + escapeHtml(stat.value) + "</span>" +
      "<p>" + escapeHtml(stat.label) + " · " + escapeHtml(stat.meta) + "</p>" +
      "</div>" +
      "</div>" +
      "</div>" +
      "</section>" +

      '<section class="demo-quote">' +
      '<div class="demo-wrap">' +
      '<span class="demo-quote__mark" aria-hidden="true">' + icon.quote + "</span>" +
      "<figure>" +
      "<blockquote>" + escapeHtml(quote.text) + "</blockquote>" +
      "<figcaption>" + escapeHtml(quote.name) + "</figcaption>" +
      "</figure>" +
      "</div>" +
      "</section>" +

      '<section class="demo-cta" id="cta">' +
      '<div class="demo-wrap">' +
      '<div class="demo-cta__panel">' +
      '<span class="demo-cta__eyebrow">' + escapeHtml(cta.eyebrow) + "</span>" +
      '<h2 class="demo-cta__title">' + rich(cta.title) + "</h2>" +
      '<p class="demo-cta__text">' + escapeHtml(cta.text) + "</p>" +
      '<a class="demo-btn" href="' + attr(cta.href) + '">' + escapeHtml(cta.button) + icon.arrow + "</a>" +
      "</div>" +
      "</div>" +
      "</section>" +
      "</main>" +

      '<footer class="demo-site__footer">' +
      '<div class="demo-site__footer-inner">' +
      '<span class="demo-site__footer-brand">' + escapeHtml(project.brand) + "</span>" +
      '<nav aria-label="Concept demo footer">' +
      '<a href="#hero">Top</a><a href="#work">Details</a><a href="#features">Approach</a><a href="index.html#projects">All projects</a>' +
      "</nav>" +
      '<p class="demo-site__footer-note">' + escapeHtml(project.footerNote) + ' · <a href="index.html#contact">Work with Imran</a></p>' +
      "</div>" +
      "</footer>";

    return site;
  }

  /* ------------------------------------------------------------------------
     Theme + metadata
     ------------------------------------------------------------------------ */
  function applyTheme(project) {
    var theme = project.theme || {};
    var map = {
      bg: "--d-bg",
      surface: "--d-surface",
      elevated: "--d-elevated",
      text: "--d-text",
      muted: "--d-muted",
      border: "--d-border",
      accent: "--d-accent",
      accent2: "--d-accent2",
      onAccent: "--d-on-accent",
      radius: "--d-radius"
    };

    Object.keys(map).forEach(function (key) {
      if (theme[key]) {
        app.style.setProperty(map[key], theme[key]);
      }
    });

    app.setAttribute("data-mode", theme.mode === "dark" ? "dark" : "light");
    app.setAttribute("data-serif", theme.serif ? "true" : "false");
    frame.style.backgroundColor = theme.bg || "#f3eee5";
    app.style.backgroundColor = theme.bg || "#f3eee5";
  }

  function updateChrome(project) {
    document.title = project.name + " — Concept demo · Imran";
    setText(barProject, project.name);
    setText(barCategory, project.category);
    setText(demoUrl, "imran.dev/demo/" + project.id);
    projectSelect.value = project.id;
  }

  function updateDetails(project) {
    setText(detailsName, project.name);
    setText(detailsYear, project.year);
    setText(detailsCategory, project.category);
    setText(detailsFocus, project.focus || "Web design & build");
    setText(detailsStack, project.stack || "WordPress · PHP · CSS · JavaScript");
    setText(
      detailsText,
      project.summary ||
        "A self-initiated concept designed and built to explore a focused idea. No real client, product or review is represented."
    );

    var metaDescription = document.querySelector('meta[name="description"]');
    if (metaDescription) {
      metaDescription.setAttribute(
        "content",
        project.name + " — an interactive concept website demo by Imran. " + (project.category || "")
      );
    }
  }

  function updatePager(project) {
    var previous = projects[neighbour(project.id, -1)];
    var next = projects[neighbour(project.id, 1)];

    if (pagerPrev && previous) {
      pagerPrev.href = "demo.html?id=" + encodeURIComponent(previous.id);
      setText(pagerPrev.querySelector("[data-pager-name]"), previous.name);
      pagerPrev.setAttribute("aria-label", "Previous concept project: " + previous.name);
    }

    if (pagerNext && next) {
      pagerNext.href = "demo.html?id=" + encodeURIComponent(next.id);
      setText(pagerNext.querySelector("[data-pager-name]"), next.name);
      pagerNext.setAttribute("aria-label", "Next concept project: " + next.name);
    }
  }

  /* ------------------------------------------------------------------------
     Source view
     ------------------------------------------------------------------------ */
  function renderCode(project) {
    if (!codeContent) {
      return;
    }

    var snippet = project.snippet || {};
    var code = snippet.code || "";
    var language = (snippet.language || "text").toLowerCase();

    setText(codeFilename, snippet.filename || "snippet.txt");
    setText(codeLanguage, languageLabels[language] || language.toUpperCase());

    codeContent.textContent = "";

    code.split("\n").forEach(function (line, index) {
      var row = document.createElement("span");
      row.className = "code-line";

      var number = document.createElement("span");
      number.className = "code-line__no";
      number.setAttribute("aria-hidden", "true");
      number.textContent = String(index + 1);

      var text = document.createElement("span");
      text.className = "code-line__text";
      text.textContent = line || " ";

      row.appendChild(number);
      row.appendChild(text);
      codeContent.appendChild(row);
    });

    if (codeBody) {
      codeBody.scrollTop = 0;
      codeBody.scrollLeft = 0;
    }
  }

  function setView(view, updateUrl) {
    if (view !== "demo" && view !== "code") {
      view = "demo";
    }

    currentView = view;

    tabs.forEach(function (tab) {
      var isCurrent = tab.getAttribute("data-view") === view;
      tab.classList.toggle("is-active", isCurrent);
      tab.setAttribute("aria-selected", isCurrent ? "true" : "false");
      tab.tabIndex = isCurrent ? 0 : -1;
    });

    Object.keys(panels).forEach(function (key) {
      if (panels[key]) {
        panels[key].hidden = key !== view;
      }
    });

    if (view === "code") {
      renderCode(projects[currentId]);
    }

    if (updateUrl !== false) {
      writeUrl();
    }

    if (status) {
      status.textContent = view === "code" ? "Showing representative source code." : "Showing live concept demo.";
    }
  }

  /* ------------------------------------------------------------------------
     URL state
     ------------------------------------------------------------------------ */
  function writeUrl() {
    try {
      var url = new URL(window.location.href);
      url.searchParams.set("id", currentId);
      if (currentView === "code") {
        url.searchParams.set("view", "code");
      } else {
        url.searchParams.delete("view");
      }
      window.history.replaceState(null, "", url.href);
    } catch (error) {
      // file:// and some embedded webviews may block replaceState. The page
      // still works; only the address bar stays out of sync.
    }
  }

  function readState() {
    var params = new URLSearchParams(window.location.search);
    var requested = params.get("id");
    var view = params.get("view");

    currentId = projects[requested] ? requested : order[0];
    currentView = view === "code" ? "code" : "demo";
  }

  /* ------------------------------------------------------------------------
     Render
     ------------------------------------------------------------------------ */
  function renderProject(project, options) {
    options = options || {};
    currentId = project.id;

    updateChrome(project);
    updateDetails(project);
    updatePager(project);
    applyTheme(project);

    app.innerHTML = "";
    app.appendChild(buildSite(project));

    frame.scrollTop = 0;
    frame.scrollLeft = 0;

    if (currentView === "code" && options.keepView !== false) {
      renderCode(project);
      setView("code", false);
    } else {
      setView("demo", false);
    }

    if (options.announce !== false && status) {
      status.textContent = "Showing " + project.name + " concept project.";
    }

    writeUrl();
  }

  /* ------------------------------------------------------------------------
     Bindings
     ------------------------------------------------------------------------ */
  order.forEach(function (id) {
    var option = document.createElement("option");
    option.value = id;
    option.textContent = projects[id].name;
    projectSelect.appendChild(option);
  });

  projectSelect.addEventListener("change", function () {
    var project = projects[projectSelect.value];
    if (project) {
      renderProject(project);
    }
  });

  Array.prototype.forEach.call(document.querySelectorAll("[data-project-step]"), function (button) {
    button.addEventListener("click", function () {
      var direction = Number(button.getAttribute("data-project-step")) || 1;
      var nextId = neighbour(currentId, direction);
      renderProject(projects[nextId]);
    });
  });

  tabs.forEach(function (tab) {
    tab.addEventListener("click", function () {
      setView(tab.getAttribute("data-view"));
    });

    tab.addEventListener("keydown", function (event) {
      var index = tabs.indexOf(tab);
      var nextIndex = null;

      if (event.key === "ArrowRight") {
        nextIndex = (index + 1) % tabs.length;
      } else if (event.key === "ArrowLeft") {
        nextIndex = (index - 1 + tabs.length) % tabs.length;
      } else if (event.key === "Home") {
        nextIndex = 0;
      } else if (event.key === "End") {
        nextIndex = tabs.length - 1;
      }

      if (nextIndex !== null) {
        event.preventDefault();
        tabs[nextIndex].focus();
        setView(tabs[nextIndex].getAttribute("data-view"));
      }
    });
  });

  if (copyButton) {
    copyButton.addEventListener("click", function () {
      var project = projects[currentId];
      var code = (project.snippet && project.snippet.code) || "";

      function markCopied() {
        showToast("Source copied to clipboard");
        copyButton.classList.add("is-copied");
        var use = copyButton.querySelector("use");
        if (use) {
          use.setAttribute("href", "#icon-check");
        }
        var label = copyButton.querySelector("span");
        if (label) {
          label.textContent = "Copied";
        }
        window.clearTimeout(copyTimer);
        copyTimer = window.setTimeout(function () {
          copyButton.classList.remove("is-copied");
          if (use) {
            use.setAttribute("href", "#icon-copy");
          }
          if (label) {
            label.textContent = "Copy";
          }
        }, 2200);
      }

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(markCopied).catch(fallbackCopy);
      } else {
        fallbackCopy();
      }

      function fallbackCopy() {
        var area = document.createElement("textarea");
        area.value = code;
        area.setAttribute("readonly", "");
        area.style.position = "fixed";
        area.style.top = "-9999px";
        area.style.opacity = "0";
        document.body.appendChild(area);
        area.select();

        var succeeded = false;
        try {
          succeeded = document.execCommand("copy");
        } catch (error) {
          succeeded = false;
        }

        document.body.removeChild(area);

        if (succeeded) {
          markCopied();
        } else {
          showToast("Press Ctrl/Cmd + C to copy the selected code");
          codeContent.focus && codeContent.focus();
          var selection = window.getSelection();
          var range = document.createRange();
          if (selection && range) {
            range.selectNodeContents(codeContent);
            selection.removeAllRanges();
            selection.addRange(range);
          }
        }
      }
    });
  }

  /* Internal anchors scroll inside the demo frame, not the outer page. */
  app.addEventListener("click", function (event) {
    var link = event.target.closest ? event.target.closest('a[href^="#"]') : null;
    if (!link) {
      return;
    }

    var hash = link.getAttribute("href");
    if (!hash || hash === "#") {
      return;
    }

    var target = app.querySelector(hash);
    if (!target) {
      return;
    }

    event.preventDefault();

    var header = app.querySelector(".demo-site__header");
    var offset = header ? header.offsetHeight + 4 : 0;
    scrollFrameTo(target, offset);

    if (hash === "#hero") {
      frame.scrollTo({ top: 0, behavior: reduceMotion.matches ? "auto" : "smooth" });
    }
  });

  /* Support deep links such as demo.html?id=lumen#story on first load. */
  window.addEventListener("load", function () {
    if (window.location.hash) {
      var target = app.querySelector(window.location.hash);
      if (target) {
        var header = app.querySelector(".demo-site__header");
        scrollFrameTo(target, header ? header.offsetHeight + 4 : 0);
      }
    }
  });

  /* ------------------------------------------------------------------------
     Boot
     ------------------------------------------------------------------------ */
  readState();
  renderProject(projects[currentId], { announce: false });
  setView(currentView, false);
})();
