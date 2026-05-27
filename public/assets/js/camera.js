const ConvexMirror = (function () {
  const W = 320;
  const H = 320;

  const canvas = document.getElementById("mirrorCanvas");
  const ctx = canvas.getContext("2d");
  const idleEl = document.getElementById("mirrorIdle");

  let strength = window.MIRROR_CONFIG?.strength ?? 0.65;

  let lutX, lutY;

  function buildLUT() {
    lutX = new Float32Array(W * H);
    lutY = new Float32Array(W * H);

    const cx = W / 2;
    const cy = H / 2;
    const maxR = cx;
    const k = strength;

    for (let y = 0; y < H; y++) {
      for (let x = 0; x < W; x++) {
        const dx = (x - cx) / maxR;
        const dy = (y - cy) / maxR;
        const r = Math.sqrt(dx * dx + dy * dy);

        let sx, sy;

        if (r >= 1) {
          sx = x;
          sy = y;
        } else {
          const rf =
            r === 0
              ? 0
              : Math.atan((r * k * Math.PI) / 2) / ((k * Math.PI) / 2);
          const scale = rf / (r + 1e-9);
          sx = cx + dx * scale * maxR;
          sy = cy + dy * scale * maxR;
        }

        const i = y * W + x;
        lutX[i] = sx;
        lutY[i] = sy;
      }
    }
  }

  function applyFisheye(srcData) {
    const sd = srcData.data;
    const dst = ctx.createImageData(W, H);
    const dd = dst.data;

    for (let y = 0; y < H; y++) {
      for (let x = 0; x < W; x++) {
        const i = y * W + x;
        const sx = lutX[i];
        const sy = lutY[i];
        const x0 = sx | 0;
        const y0 = sy | 0;
        const fx = sx - x0;
        const fy = sy - y0;

        function sample(px, py, c) {
          if (px < 0 || py < 0 || px >= W || py >= H) return 0;
          return sd[(py * W + px) * 4 + c];
        }

        const p = i * 4;
        for (let c = 0; c < 3; c++) {
          dd[p + c] =
            sample(x0, y0, c) * (1 - fx) * (1 - fy) +
            sample(x0 + 1, y0, c) * fx * (1 - fy) +
            sample(x0, y0 + 1, c) * (1 - fx) * fy +
            sample(x0 + 1, y0 + 1, c) * fx * fy;
        }
        dd[p + 3] = 255;
      }
    }

    ctx.putImageData(dst, 0, 0);
  }

  const tmp = document.createElement("canvas");
  tmp.width = W;
  tmp.height = H;
  const tmpCtx = tmp.getContext("2d");
  let video;

  function drawLoop() {
    tmpCtx.drawImage(video, 0, 0, W, H);
    applyFisheye(tmpCtx.getImageData(0, 0, W, H));
    requestAnimationFrame(drawLoop);
  }

  function init() {
    buildLUT();

    video = document.createElement("video");
    video.autoplay = true;
    video.playsInline = true;
    video.muted = true;

    navigator.mediaDevices
      .getUserMedia({
        video: { facingMode: "environment", width: W, height: H },
      })
      .catch(() => navigator.mediaDevices.getUserMedia({ video: true }))
      .then((stream) => {
        video.srcObject = stream;
        video.onloadedmetadata = () => {
          video.play();
          idleEl.classList.add("hidden");
          drawLoop();
        };
      })
      .catch((err) => {
        console.warn("Kamera ej tillgänglig:", err.message);
      });
  }

  return {
    init,

    /**
     * Justera fisheye-styrkan live.
     * Anropas från adminpanelen när slider ändras.
     * @param {number} val – värde mellan 0.0 och 1.0
     */
    setStrength(val) {
      strength = Math.max(0, Math.min(1, val));
      buildLUT();
    },

    getStrength() {
      return strength;
    },
  };
})();

document.addEventListener("DOMContentLoaded", () => ConvexMirror.init());
