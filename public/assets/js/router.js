const router = (() => {
  const cache = {};
  const layers = {};
  const hist = [];
  let current = null;

  function loadAtmPage(page) {
    const url = `/ATM/public/index.php?page=atm_` + page;

    fetch(url)
      .then((response) => response.text())
      .then((html) => {
        document.getElementById("screen-mid").innerHTML = html;
      });
  }
  function getCleanUrl(url) {
    let pageParam = "home";

    let cleanStr = url.startsWith("/") ? url.substring(1) : url;

    if (cleanStr.includes("page=")) {
      const params = new URLSearchParams(
        cleanStr.substring(cleanStr.indexOf("?")),
      );
      pageParam = params.get("page");
    } else if (cleanStr !== "" && !cleanStr.startsWith("index.php")) {
      pageParam = cleanStr;
    }

    return "/ATM/public/index.php?page=" + pageParam;
  }

  async function fetchPage(url) {
    const safeUrl = getCleanUrl(url);

    if (cache[safeUrl]) return cache[safeUrl];

    const res = await fetch(safeUrl);
    const text = await res.text();

    const parser = new DOMParser();
    const doc = parser.parseFromString(text, "text/html");
    const main = doc.querySelector("main");

    cache[safeUrl] = main ? main.innerHTML : doc.body.innerHTML;
    return cache[safeUrl];
  }

  function ensureLayer(url) {
    const safeUrl = getCleanUrl(url);
    if (layers[safeUrl]) return layers[safeUrl];

    const div = document.createElement("div");
    div.className = "page-layer";
    div.dataset.url = safeUrl;
    document.getElementById("view-container").appendChild(div);
    layers[safeUrl] = div;
    return div;
  }

  function activate(url) {
    const safeUrl = getCleanUrl(url);

    if (current && layers[current]) {
      layers[current].classList.remove("active");
    }

    if (layers[safeUrl]) {
      layers[safeUrl].classList.add("active");
      current = safeUrl;

      const cachedScript = layers[safeUrl].querySelector("script");
      if (cachedScript) {
        delete cachedScript.dataset.ran;
      }

      runScripts(layers[safeUrl]);
    }

    const backBtn = document.getElementById("back-btn");
    if (backBtn) backBtn.hidden = hist.length <= 1;
  }

  function runScripts(container) {
    container.querySelectorAll("script").forEach((old) => {
      if (old.dataset.ran) return;

      const s = document.createElement("script");
      [...old.attributes].forEach((a) => s.setAttribute(a.name, a.value));
      s.textContent = old.textContent;
      old.replaceWith(s);
      s.dataset.ran = "1";
    });
  }

  function hijackLinks(container) {
    container.querySelectorAll("a[href]").forEach((a) => {
      const href = a.getAttribute("href");
      if (!href || href.startsWith("http") || href.startsWith("#")) return;

      a.addEventListener("click", (e) => {
        e.preventDefault();

        const hasZoom = !!container.querySelector(".page-overlay");

        if (hasZoom) {
          triggerZoomThenNavigate(container, a, href);
        } else {
          router.navigate(href);
        }
      });
    });
  }

  function triggerZoomThenNavigate(container, anchor, href) {
    window.__routerBlocking = true;
    window.__routerTarget = null;

    anchor.dispatchEvent(
      new MouseEvent("click", {
        bubbles: true,
        cancelable: false,
      }),
    );

    setTimeout(() => {
      window.__routerBlocking = false;
      router.navigate(href);
    }, 1850);
  }

  return {
    async init(startUrl) {
      window.addEventListener("popstate", (e) => {
        if (e.state?.url) this.navigate(e.state.url, false);
      });
      await this.navigate(startUrl, false);
    },

    async navigate(url, pushState = true) {
      const safeUrl = getCleanUrl(url);
      const html = await fetchPage(safeUrl);
      const layer = ensureLayer(safeUrl);

      if (!layer.dataset.loaded) {
        layer.innerHTML = html;
        layer.dataset.loaded = "1";
        hijackLinks(layer);
      }

      if (pushState) {
        window.history.pushState({ url: safeUrl }, "", safeUrl);
      }
      hist.push(safeUrl);
      activate(safeUrl);
    },

    loadAtmPage(page) {
      const safeUrl = getCleanUrl("atm_" + page);

      fetch(safeUrl)
        .then((response) => response.text())
        .then((html) => {
          const screenMid = document.getElementById("screen-mid");
          if (screenMid) {
            screenMid.innerHTML = html;
            console.log(
              "Bankomatskärm uppdaterad via säker sökväg: " + safeUrl,
            );
          } else {
            console.error("Hittade inte #screen-mid på sidan!");
          }
        })
        .catch((err) =>
          console.error("Router-fel vid laddning av skärm:", err),
        );
    },

    back() {
      if (hist.length <= 1) return;
      hist.pop();
      const prev = hist[hist.length - 1];
      window.history.back();
      activate(prev);
    },

    goTo(url) {
      this.navigate(url);
    },
  };
})();
