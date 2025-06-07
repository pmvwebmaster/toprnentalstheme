from fastapi import FastAPI
from pydantic import BaseModel
from typing import Optional

app = FastAPI()

class Item(BaseModel):
    name: str
    description: Optional[str] = None
    price: float
    in_stock: bool = True

@app.get("/")
def read_root():
    return {"message": "Olá, mundo!"}

@app.post("/items/")
def create_item(item: Item):
    return {"item": item}
