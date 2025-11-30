document.addEventListener("DOMContentLoaded", () => {
  const t = document.querySelectorAll(".toast");
  t.forEach((el) => new bootstrap.Toast(el).show());
});
