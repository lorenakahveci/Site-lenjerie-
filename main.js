$(document).ready(function () {
  // LOGIN / REGISTER MODAL
  var modal = $("#loginModal");
  $("#loginBtn").click(() => modal.show());
  $(".close").click(() => modal.hide());

  $("#signinBtn").click(function () {
    $.ajax({
      url: "account/login.php",
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify({
        email: $("#signin-email").val(),
        pass: $("#signin-password").val(),
      }),
      success: function (res) {
        if (res.success) location.reload();
        else alert(res.msg);
      },
    });
  });

  $("#signupBtn").click(function () {
    $.ajax({
      url: "account/register.php",
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify({
        name: $("#signup-name").val(),
        email: $("#signup-email").val(),
        pass: $("#signup-password").val(),
      }),
      success: function (res) {
        if (res.success) location.reload();
        else alert(res.msg);
      },
    });
  });

  // CART MODAL
  var cartModal = $("#cartModal");
  $("#cartBtn").click(function () {
    loadCart();
    cartModal.show();
  });
  $(".closeCart").click(() => cartModal.hide());

  function loadCart() {
    $.getJSON("includes/get_cart.php", function (res) {
      if (res.success) {
        var html = "";
        res.items.forEach(function (item) {
          html += `<div class="cart-item">
                        <img src="${
                          item.image || "assets/images/default.png"
                        }" width="50" />
                        ${item.name} - ${item.price} lei x 
                        <button class="minus" data-id="${item.id}">-</button>
                        ${item.quantity}
                        <button class="plus" data-id="${item.id}">+</button>
                        <button class="remove" data-id="${
                          item.id
                        }">Șterge</button>
                    </div>`;
        });
        $("#cartItems").html(html);
        $("#cartTotal").text(res.total);
      } else {
        $("#cartItems").html("<p>Coș gol</p>");
        $("#cartTotal").text("0.00");
      }
    });
  }

  // CART + / - / remove
  $("#cartItems").on("click", ".plus", function () {
    var id = parseInt($(this).data("id"));
    $.ajax({
      url: "includes/update_cart.php",
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify({ id: id, op: "plus" }),
      success: loadCart,
    });
  });

  $("#cartItems").on("click", ".minus", function () {
    var id = parseInt($(this).data("id"));
    $.ajax({
      url: "includes/update_cart.php",
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify({ id: id, op: "minus" }),
      success: loadCart,
    });
  });

  $("#cartItems").on("click", ".remove", function () {
    var id = parseInt($(this).data("id"));
    $.ajax({
      url: "includes/remove_from_cart.php",
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify({ id: id }),
      success: loadCart,
    });
  });

  // ADD TO CART
  $(".add-to-cart").click(function () {
    var id = parseInt($(this).data("id"));
    if (!id) {
      alert("Produs invalid");
      return;
    }

    $.ajax({
      url: "includes/add_to_cart.php",
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify({ id: id, quantity: 1 }),
      success: function (res) {
        if (res.success) {
          alert("Produs adăugat!");
          loadCart(); // update coș
        } else {
          alert(res.msg);
        }
      },
    });
  });

  // CHECKOUT
  $("#checkoutBtn").click(function () {
    window.location.href = "create_checkout_session.php";
  });
});
const heroSlides = document.querySelectorAll(".hero-slide");
const heroDotsContainer = document.querySelector(".hero-dots");

let currentHero = 0;

// Cream bulinele
heroSlides.forEach((_, i) => {
  const dot = document.createElement("span");
  if (i === 0) dot.classList.add("active");
  heroDotsContainer.appendChild(dot);
});

const heroDots = heroDotsContainer.querySelectorAll("span");

// Functia de schimbare
function changeHeroSlide(index) {
  heroSlides.forEach((s) => s.classList.remove("active"));
  heroDots.forEach((d) => d.classList.remove("active"));
  heroSlides[index].classList.add("active");
  heroDots[index].classList.add("active");
}

function nextHero() {
  currentHero = (currentHero + 1) % heroSlides.length;
  changeHeroSlide(currentHero);
}

let heroAuto = setInterval(nextHero, 5000);

// Click pe buline
heroDots.forEach((dot, i) => {
  dot.addEventListener("click", () => {
    currentHero = i;
    changeHeroSlide(i);
    clearInterval(heroAuto);
    heroAuto = setInterval(nextHero, 5000);
  });
});
