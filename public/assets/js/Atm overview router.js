const overlay = document.getElementById("overlay");
const bgImg = document.getElementById("atm-bg");

document.querySelectorAll(".atm-hotspot").forEach((link) => {
  link.addEventListener("click", function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    const href = this.getAttribute("href");

    // Räkna ut mitten av den klickade bankomaten
    const rect = this.getBoundingClientRect();
    const container = document
      .querySelector(".image-container")
      .getBoundingClientRect();
    const cx =
      ((rect.left + rect.width / 2 - container.left) / container.width) * 100;
    const cy =
      ((rect.top + rect.height / 2 - container.top) / container.height) * 100;

    bgImg.style.transformOrigin = `${cx}% ${cy}%`;
    bgImg.classList.add("zoom-to-atm");

    setTimeout(() => overlay.classList.add("fade-out"), 1200);

    setTimeout(() => {
      bgImg.classList.remove("zoom-to-atm");
      overlay.classList.remove("fade-out");
      bgImg.style.transformOrigin = "center center";

      let targetHref = href;
      if (!targetHref.startsWith("index.php")) {
        targetHref = "index.php?page=" + targetHref;
      }

      router.goTo(targetHref);
    }, 1800);
  });
});
