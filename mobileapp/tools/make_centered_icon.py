"""Build app icon with branding text centered in the mobile safe zone."""
from __future__ import annotations

from pathlib import Path

import numpy as np
from PIL import Image, ImageDraw, ImageFilter

SRC = Path(
    r"C:\Users\MUJTABA LAPTOPS\.cursor\projects\d-wamp-www-kts\assets"
    r"\c__Users_MUJTABA_LAPTOPS_AppData_Roaming_Cursor_User_workspaceStorage_"
    r"empty-window_images_image-46c3f8ea-e4da-4e6b-bc0c-2649e4bf40aa.png"
)
OUT = Path(__file__).resolve().parents[1] / "assets" / "icon" / "app_icon.png"
SIZE = 1024


def find_banner_top(rgb: np.ndarray) -> int:
    """Detect where the dark green text banner begins."""
    h, w, _ = rgb.shape
    brightness = rgb.astype(np.float32).mean(axis=2)
    row_mean = brightness.mean(axis=1)
    best_y = int(h * 0.68)
    best_score = -1.0
    for y in range(int(h * 0.50), int(h * 0.88)):
        above = row_mean[max(0, y - 10) : y].mean()
        below = row_mean[y : min(h, y + 10)].mean()
        score = above - below
        if score > best_score:
            best_score = score
            best_y = y
    return best_y


def rounded_mask(size: int, radius: int) -> Image.Image:
    mask = Image.new("L", (size, size), 0)
    draw = ImageDraw.Draw(mask)
    draw.rounded_rectangle((0, 0, size - 1, size - 1), radius=radius, fill=255)
    return mask


def main() -> None:
    src = Image.open(SRC).convert("RGBA")
    src = src.resize((SIZE, SIZE), Image.Resampling.LANCZOS)
    arr = np.array(src)
    rgb = arr[:, :, :3]

    banner_top = find_banner_top(rgb)
    print(f"banner_top={banner_top}")

    # Content inset inside glossy green frame (~6%)
    inset = int(SIZE * 0.055)
    inner = SIZE - 2 * inset

    # Photo region: from below top border to banner
    photo = src.crop((inset, inset, SIZE - inset, banner_top))
    # Scale photo to fill the full inner square (will be darkened under text)
    photo_bg = photo.resize((inner, inner), Image.Resampling.LANCZOS)

    # Extract banner strip with text
    banner = src.crop((inset, banner_top, SIZE - inset, SIZE - inset))
    bw, bh = banner.size

    # Target banner height ~32% of inner area, centered
    target_h = int(inner * 0.34)
    banner_scaled = banner.resize((inner, target_h), Image.Resampling.LANCZOS)

    # Soft vertical fade on banner edges so it blends into photo
    fade = Image.new("L", (inner, target_h), 255)
    fade_draw = ImageDraw.Draw(fade)
    fade_px = max(8, target_h // 12)
    for i in range(fade_px):
        a = int(255 * (i / fade_px))
        fade_draw.line([(0, i), (inner, i)], fill=a)
        fade_draw.line([(0, target_h - 1 - i), (inner, target_h - 1 - i)], fill=a)
    banner_scaled.putalpha(fade)

    # Compose: dark green base + photo + centered banner
    canvas = Image.new("RGBA", (SIZE, SIZE), (0, 0, 0, 255))

    # Outer glossy green frame from original (keep border look)
    frame = src.copy()

    # Build inner content
    inner_img = Image.new("RGBA", (inner, inner), (10, 45, 30, 255))
    # Slightly darkened photo so white text stays readable in center
    photo_dim = photo_bg.point(lambda p: int(p * 0.72))
    inner_img.paste(photo_dim, (0, 0))

    # Center the text banner vertically
    by = (inner - target_h) // 2
    inner_img.alpha_composite(banner_scaled, (0, by))

    # Paste inner into frame area
    frame.paste(inner_img, (inset, inset))

    # Re-apply rounded outer mask so corners stay clean on black
    mask = rounded_mask(SIZE, radius=int(SIZE * 0.22))
    out = Image.new("RGBA", (SIZE, SIZE), (0, 0, 0, 255))
    out.paste(frame, (0, 0))
    out.putalpha(mask)

    # Flatten onto dark green (no transparency for launcher tools)
    flat = Image.new("RGB", (SIZE, SIZE), (8, 40, 28))
    flat.paste(out, mask=out.split()[-1])

    OUT.parent.mkdir(parents=True, exist_ok=True)
    flat.save(OUT, "PNG", optimize=True)
    print(f"wrote {OUT} ({OUT.stat().st_size} bytes)")


if __name__ == "__main__":
    main()
