let cart = [];

const calculateTotal = () => {
  let total = 0;
  cart.forEach((item) => {
    total += item.price;
  });
  return total;
};

const formatPrice = (price) => {
  return price.toLocaleString("ru-RU") + " ₽";
};

const removeFromCart = (index) => {
  cart.splice(index, 1);
  renderCart();
};

const renderCart = () => {
  const cartItems = document.querySelector("#cart-items");
  const cartTotal = document.querySelector("#cart-total");

  cartItems.innerHTML = "";

  cart.forEach((item, index) => {
    const li = document.createElement("li");
    li.textContent = item.name + " — " + formatPrice(item.price) + " ";

    const removeBtn = document.createElement("button");
    removeBtn.textContent = "✕";
    removeBtn.className = "btn-remove";
    removeBtn.addEventListener("click", () => {
      removeFromCart(index);
    });

    li.appendChild(removeBtn);
    cartItems.appendChild(li);
  });

  const total = calculateTotal();
  cartTotal.textContent = "Итого: " + formatPrice(total);
};

const addToCart = (product) => {
  cart.push(product);
  renderCart();
};

const filterProducts = (category) => {
  const products = document.querySelectorAll(".product");

  products.forEach((product) => {
    if (category === "all" || product.dataset.category === category) {
      product.style.display = "";
    } else {
      product.style.display = "none";
    }
  });
};

const addButtons = document.querySelectorAll(".btn-add-to-cart");

addButtons.forEach((button) => {
  button.addEventListener("click", () => {
    const productElement = button.closest(".product");
    const name = productElement.dataset.name;
    const price = Number(productElement.dataset.price);

    addToCart({ name, price });
  });
});

const categoryFilter = document.querySelector("#category-filter");
if (categoryFilter) {
  categoryFilter.addEventListener("change", () => {
    const selectedCategory = categoryFilter.value;
    filterProducts(selectedCategory);
  });
}

const payButton = document.querySelector("#btn-pay");
if (payButton) {
  payButton.addEventListener("click", () => {
    if (cart.length === 0) {
      alert("Корзина пуста!");
    } else {
      alert("Покупка прошла успешно!");
      cart = [];
      renderCart();
    }
  });
}

const clearButton = document.querySelector("#btn-clear-cart");
if (clearButton) {
  clearButton.addEventListener("click", () => {
    cart = [];
    renderCart();
  });
}
