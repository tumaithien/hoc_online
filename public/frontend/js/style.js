$(function(){
    window.addEventListener("load", function(e) {
        (function(){
            function scrollNav() {
                let headerNav = document.querySelector(".header-nav");
                let hNavOffsetY = headerNav.getBoundingClientRect().bottom;
                let navMenu = document.querySelector(".nav-menu-1");
                if(window.pageYOffset > 120) {
                    headerNav.classList.add("stick");
                } else  if(window.pageYOffset < 30){
                    headerNav.classList.remove("stick");
                }
                if(window.innerWidth < 992) {
                    headerNav.after(navMenu)
                } else {
                    document.querySelector(".nav-desktop-mobile").append(navMenu);
                }
            }
            window.addEventListener("scroll",scrollNav)
            scrollNav();
            // 
            $(".dropdown-nav").on("mouseenter", function(e){
                if(window.innerWidth < 992) return;
                this.classList.add("show")
            })
            $(".dropdown-nav").on("mouseleave", function(e){
                if(window.innerWidth < 992) return;
                this.classList.remove("show")
            })
            // 
            $(".dropdown-nav").on("click", function(e){
                e.stopPropagation();
                if(window.innerWidth >= 992) return;
                let parentNav = this.parentElement ;
                let othershows = parentNav.querySelectorAll(".dropdown-nav.show");
                for(let ele of othershows) {
                    if(ele === this) continue;
                    ele.classList.remove("show");
                    
                }
                console.log(this.classList.contains("show"));
                this.classList.toggle("show");
            })
            // 
            $(".btn-open-nav").on("click", function(e) {
                $(".nav-menu-1").addClass("show");
                document.body.style.overflow = "hidden";
            })
            $(".nav-menu-1").on("click", function(e) {
                if(window.innerWidth >= 992) return;
                if(e.target.closest(".nav-menu-wrap")) return;
                this.classList.remove("show");
                let othershows = document.querySelectorAll(".nav-menu-1 .dropdown-nav.show");
                for(let ele of othershows) {
                    if(ele === this) continue;
                    ele.classList.remove("show");
                    
                }
                document.body.style.overflow = "";
            })
       
        })()
    })

    var hideshowpass = function(){
        $(".hide-show-pass").on("click", function(e){
            if(!e.target.classList.contains("img-eye")) return;
            if(this.querySelector("input[type='password']")) {
                this.querySelector("input[type='password']").setAttribute("type", "text");
                return;
            }
            if(this.querySelector("input[type='text']")) {
                this.querySelector("input[type='text']").setAttribute("type", "password");
                return;
            }
        });
    }

    hideshowpass();
    
});