/**
 * Máscara de telefone brasileiro: (XX) XXXXX-XXXX ou (XX) XXXX-XXXX
 */
document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("customer_phone");
  if (!input) return;

  input.addEventListener("input", (e) => {
    let value = e.target.value.replace(/\D/g, "");
    if (value.length > 11) value = value.slice(0, 11);
    if (value.length <= 2) {
      e.target.value = value ? "(" + value : "";
    } else if (value.length <= 6) {
      e.target.value = "(" + value.slice(0, 2) + ") " + value.slice(2);
    } else {
      e.target.value = "(" + value.slice(0, 2) + ") " + value.slice(2, 7) + "-" + value.slice(7);
    }
  });

  input.addEventListener("keydown", (e) => {
    if (e.key === "Backspace" && e.target.value.length <= 4) {
      e.target.value = e.target.value.replace(/\D/g, "").slice(0, -1);
      if (e.target.value.length <= 2) {
        e.target.value = e.target.value ? "(" + e.target.value : "";
      }
    }
  });
});
