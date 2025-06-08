from fastapi import FastAPI
from pydantic import BaseModel
from typing import Optional
import os
import httpx
from fastapi.staticfiles import StaticFiles

app = FastAPI()

class Item(BaseModel):
    name: str
    description: Optional[str] = None
    price: float
    in_stock: bool = True

class ChatRequest(BaseModel):
    prompt: str

@app.get("/")
def read_root():
    return {"message": "Olá, mundo!"}

@app.post("/items/")
def create_item(item: Item):
    return {"item": item}

@app.post("/chat/")
def chat_with_hf(request: ChatRequest):
    api_url = "https://api-inference.huggingface.co/models/distilbert-base-uncased-finetuned-sst-2-english"
    headers = {"Authorization": f"Bearer {os.getenv('HF_API_TOKEN', '')}"}
    response = httpx.post(api_url, headers=headers, json={"inputs": request.prompt})
    if response.status_code == 200:
        return {"result": response.json()}
    else:
        return {"error": "Erro ao consultar HuggingFace API", "details": response.text}

@app.post("/sentiment/")
def sentiment_via_huggingface(request: ChatRequest):
    api_url = "https://api-inference.huggingface.co/models/distilbert-base-uncased-finetuned-sst-2-english"
    headers = {"Authorization": f"Bearer {os.getenv('HF_API_TOKEN', '')}"}
    response = httpx.post(api_url, headers=headers, json={"inputs": request.prompt})
    if response.status_code == 200:
        return {"result": response.json()}
    else:
        return {"error": "Erro ao consultar HuggingFace API", "details": response.text}

app.mount("/", StaticFiles(directory="static", html=True), name="static")
