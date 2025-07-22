function updateCartCount(count) {
  const cartCountElements = document.querySelectorAll("#cart-count");
  cartCountElements.forEach((el) => {
    el.textContent = count;
  });
}
