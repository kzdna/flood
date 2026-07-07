from flask import Flask, jsonify, request
from flask_cors import CORS

import os
import gdown
import uuid
import numpy as np
from PIL import Image

import torch
import torch.nn as nn

import torchvision.transforms as transforms

from torchvision.models.segmentation import (
    deeplabv3_resnet50,
    DeepLabV3_ResNet50_Weights,
)

app = Flask(__name__)
CORS(app)

DEVICE = torch.device("cuda" if torch.cuda.is_available() else "cpu")

MODEL_PATH = "best_deeplabv3_flood.pth"

if not os.path.exists(MODEL_PATH):
    gdown.download(
        "https://drive.google.com/uc?id=1mfoUWVcW7AeYquFMUvzxCnKQJYJ1BP0Y",
        MODEL_PATH,
        quiet=False
    )

UPLOAD_FOLDER = "uploads"
RESULT_FOLDER = "results"

os.makedirs(UPLOAD_FOLDER, exist_ok=True)
os.makedirs(RESULT_FOLDER, exist_ok=True)

def build_deeplabv3():

    model = deeplabv3_resnet50(
        weights=DeepLabV3_ResNet50_Weights.DEFAULT
    )

    in_channels = model.classifier[4].in_channels
    model.classifier[4] = nn.Conv2d(
        in_channels,
        2,
        kernel_size=1
    )

    in_channels_aux = model.aux_classifier[4].in_channels
    model.aux_classifier[4] = nn.Conv2d(
        in_channels_aux,
        2,
        kernel_size=1
    )

    return model

model = build_deeplabv3()

checkpoint = torch.load(
    MODEL_PATH,
    map_location=DEVICE,
    weights_only=False
)

model.load_state_dict(
    checkpoint["model_state_dict"]
)

model.to(DEVICE)

model.eval()

print("✅ Model berhasil dimuat")

transform = transforms.Compose([
    transforms.Resize((512, 512)),
    transforms.ToTensor(),
    transforms.Normalize(
        mean=(0.485, 0.456, 0.406),
        std=(0.229, 0.224, 0.225)
    )
])


def predict_single(image_path):

    img_pil = Image.open(image_path).convert("RGB")

    w, h = img_pil.size

    img_orig = np.array(img_pil)

    img_tensor = transform(
        img_pil
    ).unsqueeze(0).to(DEVICE)

    with torch.no_grad():

        output = model(img_tensor)["out"]

        prob_map = torch.softmax(
            output,
            dim=1
        )[0, 1].cpu().numpy()

    pred_mask = (
        prob_map > 0.5
    ).astype(np.uint8)

    confidence = float(
        prob_map.mean() * 100
    )

    flood_area = float(
        pred_mask.mean() * 100
    )

    mask_resized = Image.fromarray(
        (pred_mask * 255).astype(np.uint8)
    ).resize(
        (w, h),
        Image.NEAREST
    )

    mask_np = np.array(mask_resized)

    overlay = img_orig.copy()

    overlay[mask_np > 0] = [
        0,
        100,
        255
    ]

    overlay = (
        overlay * 0.5 +
        img_orig * 0.5
    ).astype(np.uint8)

    return (
        img_orig,
        pred_mask,
        overlay,
        confidence,
        flood_area
    )

@app.get("/")
def home():
    return jsonify({
        "message": "Flood Detection API Running"
    })


@app.get("/api/health")
def health():
    return jsonify({
        "status": "ok"
    })

@app.post("/api/predict")
def predict():

    if "image" not in request.files:
        return jsonify({
            "success": False,
            "message": "Tidak ada gambar."
        }), 400

    image = request.files["image"]

    filename = f"{uuid.uuid4()}.png"
    image_path = os.path.join(
        UPLOAD_FOLDER,
        filename
    )

    image.save(image_path)

    img_orig, pred_mask, overlay, confidence, flood_area = predict_single(
        image_path
    )

    prediction = (
        "Flood"
        if flood_area >= 5
        else "No Flood"
    )

    overlay_name = f"overlay_{filename}"

    overlay_path = os.path.join(
        RESULT_FOLDER,
        overlay_name
    )

    Image.fromarray(
        overlay
    ).save(
        overlay_path
    )

    mask_name = f"mask_{filename}"

    mask_path = os.path.join(
        RESULT_FOLDER,
        mask_name
    )

    Image.fromarray(
        (pred_mask * 255).astype(np.uint8)
    ).save(
        mask_path
    )

    return jsonify({

        "success": True,

        "prediction": prediction,

        "confidence": round(
            confidence,
            2
        ),

        "flood_area": round(
            flood_area,
            2
        ),

        "filename": filename,

        "overlay": overlay_name,

        "mask": mask_name

    })

import os

if __name__ == "__main__":
    app.run(
        host="0.0.0.0",
        port=int(os.environ.get("PORT", 8080)),
        debug=False
    )