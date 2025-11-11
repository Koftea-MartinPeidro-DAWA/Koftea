document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form"); // Selecciona tu formulario
    const estado = document.getElementById("mensaje-estado");
    const validarJS = document.getElementById("validar-js");

    form.addEventListener("submit", (e) => {

        // Solo interceptar el submit si el checkbox está marcado
        if (validarJS.checked) {
            e.preventDefault(); // evita el envío a PHP

            // Obtener valores
            const nombre = document.getElementById("nombre").value.trim();
            const email = document.getElementById("email").value.trim();
            const mensaje = document.getElementById("mensaje").value.trim();
            const archivo = document.getElementById("upload").files[0];
            const acepto = document.getElementById("acepto").checked;

            // Validar campos obligatorios
            if (!nombre || !email || !mensaje) {
                estado.textContent = "Por favor, completa todos los campos obligatorios.";
                estado.style.color = "red";
                return;
            }

            // Validar correo
            const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!regexEmail.test(email)) {
                estado.textContent = "Introduce un correo electrónico válido.";
                estado.style.color = "red";
                return;
            }

            // Validar aceptación de términos
            if (!acepto) {
                estado.textContent = "Debes aceptar los términos y condiciones.";
                estado.style.color = "red";
                return;
            }

            // Validar extensión de archivo (opcional)
            if (archivo) {
                const extPermitidas = ["jpg", "jpeg", "png", "pdf"];
                const extArchivo = archivo.name.split(".").pop().toLowerCase();
                if (!extPermitidas.includes(extArchivo)) {
                    estado.textContent = "Tipo de archivo no permitido. Solo JPG, PNG o PDF.";
                    estado.style.color = "red";
                    return;
                }
            }

            // Si pasa todas las validaciones
            estado.textContent = "¡Gracias por tu mensaje! Nos pondremos en contacto pronto.";
            estado.style.color = "green";

            // Opcional: resetear formulario
            form.reset();
        }
        // Si no está marcado, se envía normalmente a PHP
    });
});
