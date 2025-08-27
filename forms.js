
// الحصول على العناصر
var showFormBtns = document.querySelectorAll(".showFormBtn");
var forms = document.querySelectorAll(".popupForm");
var overlay = document.getElementById("overlay");
var closeBtns = document.querySelectorAll(".closeBtn");

// إظهار النموذج والخلفية المغبشة عند الضغط على الزر المناسب
showFormBtns.forEach(function(btn) {
    btn.addEventListener("click", function() {
        var formId = this.getAttribute("data-form");
        forms.forEach(function(form) {
            form.style.display = "none"; // إخفاء جميع النماذج
        });
        document.getElementById(formId).style.display = "flex"; // إظهار النموذج المطلوب
        overlay.style.display = "flex"; // إظهار الخلفية المغبشة
    });
});

// إخفاء النموذج والخلفية المغبشة عند الضغط على زر الإغلاق
closeBtns.forEach(function(btn) {
    btn.addEventListener("click", function() {
        this.parentElement.style.display = "none";
        overlay.style.display = "none";
    });
});

// إخفاء النموذج عند الضغط خارج النموذج
overlay.addEventListener("click", function() {
    forms.forEach(function(form) {
        form.style.display = "none"; // إخفاء جميع النماذج
    });
    overlay.style.display = "none"; // إخفاء الخلفية المغبشة
});
