from fastapi import FastAPI
from pydantic import BaseModel
from typing import Optional
import httpx

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
async def chat_with_ollama(request: ChatRequest):
    async with httpx.AsyncClient() as client:
        response = await client.post(
            "http://localhost:11434/api/generate",  # Ajuste para o endpoint do Ollama
            json={"model": "llama2", "prompt": request.prompt}
        )
        data = response.json()
    return {"response": data.get("response", "Erro ao obter resposta do modelo.")}
