$(function () {
  //   window.addEventListener("load", function (e) {
  //     (function () {
  //       function scrollNav() {
  //         let headerNav = document.querySelector(".header-nav");
  //         let hNavOffsetY = headerNav.getBoundingClientRect().bottom;
  //         let navMenu = document.querySelector(".nav-menu-1");
  //         if (window.pageYOffset > 120) {
  //           headerNav.classList.add("stick");
  //         } else if (window.pageYOffset < 30) {
  //           headerNav.classList.remove("stick");
  //         }
  //         if (window.innerWidth < 992) {
  //           headerNav.after(navMenu);
  //         } else {
  //           document.querySelector(".nav-desktop-mobile").append(navMenu);
  //         }
  //       }
  //       window.addEventListener("scroll", scrollNav);
  //       scrollNav();
  //       //
  //       $(".dropdown-nav").on("mouseenter", function (e) {
  //         if (window.innerWidth < 992) return;
  //         this.classList.add("show");
  //       });
  //       $(".dropdown-nav").on("mouseleave", function (e) {
  //         if (window.innerWidth < 992) return;
  //         this.classList.remove("show");
  //       });
  //       //
  //       $(".dropdown-nav").on("click", function (e) {
  //         e.stopPropagation();
  //         if (window.innerWidth >= 992) return;
  //         let parentNav = this.parentElement;
  //         let othershows = parentNav.querySelectorAll(".dropdown-nav.show");
  //         for (let ele of othershows) {
  //           if (ele === this) continue;
  //           ele.classList.remove("show");
  //         }
  //         console.log(this.classList.contains("show"));
  //         this.classList.toggle("show");
  //       });
  //       //
  //       $(".btn-open-nav").on("click", function (e) {
  //         $(".nav-menu-1").addClass("show");
  //         document.body.style.overflow = "hidden";
  //       });
  //       $(".nav-menu-1").on("click", function (e) {
  //         if (window.innerWidth >= 992) return;
  //         if (e.target.closest(".nav-menu-wrap")) return;
  //         this.classList.remove("show");
  //         let othershows = document.querySelectorAll(
  //           ".nav-menu-1 .dropdown-nav.show"
  //         );
  //         for (let ele of othershows) {
  //           if (ele === this) continue;
  //           ele.classList.remove("show");
  //         }
  //         document.body.style.overflow = "";
  //       });
  //     })();
  //   });

  var hideshowpass = function () {
    $(".hide-show-pass").on("click", function (e) {
      if (!e.target.classList.contains("img-eye")) return;
      if (this.querySelector("input[type='password']")) {
        this.querySelector("input[type='password']").setAttribute(
          "type",
          "text"
        );
        return;
      }
      if (this.querySelector("input[type='text']")) {
        this.querySelector("input[type='text']").setAttribute(
          "type",
          "password"
        );
        return;
      }
    });
  };

  hideshowpass();

  ("use strict");

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim();
    if (all) {
      return [...document.querySelectorAll(el)];
    } else {
      return document.querySelector(el);
    }
  };

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all);
    if (selectEl) {
      if (all) {
        selectEl.forEach((e) => e.addEventListener(type, listener));
      } else {
        selectEl.addEventListener(type, listener);
      }
    }
  };

  /**
   * Mobile nav toggle
   */
  on("click", ".mobile-nav-toggle", function (e) {
    select("#navbar").classList.toggle("navbar-mobile");
    this.classList.toggle("bi-list");
    this.classList.toggle("bi-x");
  });

  /**
   * Mobile nav dropdowns activate
   */
  on(
    "click",
    ".navbar .dropdown > a",
    function (e) {
      if (select("#navbar").classList.contains("navbar-mobile")) {
        e.preventDefault();
        this.nextElementSibling.classList.toggle("dropdown-active");
      }
    },
    true
  );
  /**
   * Toggle Pass
   */
  on("click", ".toggle-pass", function (e) {
    this.closest("._input--pasword").classList.toggle("active");
    if (select("._input--pasword").classList.contains("active")) {
      this.previousElementSibling.setAttribute("type", "text");
    } else {
      this.previousElementSibling.setAttribute("type", "password");
    }
  });

  on("click", ".videoCourse_collapsible", function (e) {
    console.log("true :>> ", true);
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.maxHeight) {
      content.style.maxHeight = null;
    } else {
      content.style.maxHeight = content.scrollHeight + "px";
    }
  });

  window.addEventListener("load", () => {
    AOS.init({
      duration: 1000,
      easing: "ease-in-out",
      once: true,
      mirror: false,
    });
  });

  new PureCounter();
});

$(".section-slick").slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  autoplay: false,
  autoplaySpeed: 6000,
  mobileFirst: true,
  arrows: false,
  dots: true,
  responsive: [
    {
      breakpoint: 767,
      settings: "unslick",
    },
  ],
});
