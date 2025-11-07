document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("profile-form");
    const submitBtn = document.getElementById("submit-btn");

    if (!form || !submitBtn) {
        return;
    }

    const validators = {
        name: (value) =>
            value.trim().length >= 2 || "Debe tener al menos 2 caracteres",
        dni: (value) =>
            /^[0-9-]{6,20}$/.test(value.trim()) ||
            "DNI inválido (solo números y guiones, 6-20 caracteres)",
        select: (value) => value !== "" || "Este campo es obligatorio",
        avatar: (file) => {
            if (!file) return true;
            const allowed = [
                "image/jpeg",
                "image/jpg",
                "image/png",
                "image/webp",
            ];
            if (!allowed.includes(file.type)) return "Formato no permitido";
            if (file.size > 5 * 1024 * 1024)
                return "La imagen debe ser menor a 5MB";
            return true;
        },
    };

    let dniValidation = {
        isValidating: false,
        isAvailable: null,
        lastChecked: null,
        debounceTimer: null,
    };

    const touched = {};
    let triedSubmit = false;

    async function validateDniAvailability(dni, { decorateUI = true } = {}) {
        if (!dni || dni.length < 6) return true;

        const dniInput = document.getElementById("dni");
        const loadingEl = document.querySelector("[data-dni-loading]");

        dniValidation.isValidating = true;

        if (decorateUI && loadingEl) loadingEl.classList.remove("hidden");

        try {
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");
            const response = await fetch("/cliente/api/validar-dni", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({ dni }),
            });

            const result = await response.json();
            dniValidation.isAvailable = !!result.disponible;

            if (!dniValidation.isAvailable && decorateUI) {
                showError(
                    dniInput,
                    result.mensaje || "Este DNI ya está registrado"
                );
            }

            return dniValidation.isAvailable;
        } catch (error) {
            console.error("Error validando DNI:", error);
            if (decorateUI) {
                showError(
                    dniInput,
                    "Error al validar DNI. Inténtalo de nuevo."
                );
            }
            return false;
        } finally {
            dniValidation.isValidating = false;
            if (decorateUI && loadingEl) loadingEl.classList.add("hidden");
        }
    }

    function showDniSuccess(message) {
        const dniInput = document.getElementById("dni");
        const successEl = document.querySelector("[data-dni-success]");

        if (successEl) {
            successEl.innerHTML = `
                <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                ${message}
            `;
            successEl.classList.remove("hidden");
        }

        dniInput.classList.remove(
            "border-red-500",
            "border-gray-300",
            "dark:border-gray-600"
        );
        dniInput.classList.add("border-green-500");
    }

    function hideDniSuccess() {
        const successEl = document.querySelector("[data-dni-success]");
        if (successEl) {
            successEl.classList.add("hidden");
        }

        const dniInput = document.getElementById("dni");
        if (dniInput) {
            dniInput.classList.remove("border-green-500");
            dniInput.classList.add("border-gray-300", "dark:border-gray-600");
        }
    }

    function showError(input, message) {
        const el = document.querySelector(
            `[data-client-error-for="${input.id}"]`
        );
        if (el) {
            el.textContent = message;
            el.classList.remove("hidden");
        }
        if (input.id === "dni") {
            const successEl = document.querySelector("[data-dni-success]");
            successEl?.classList.add("hidden");
            dniValidation.isAvailable = false;
        }
        input.classList.add("border-red-500");
        input.classList.remove(
            "border-gray-300",
            "dark:border-gray-600",
            "border-green-500"
        );
    }

    function clearError(input) {
        const el = document.querySelector(
            `[data-client-error-for="${input.id}"]`
        );
        if (el) {
            el.textContent = "";
            el.classList.add("hidden");
        }

        if (input.id === "dni" && dniValidation.isAvailable === true) {
            const successEl = document.querySelector("[data-dni-success]");
            successEl?.classList.remove("hidden");
            input.classList.remove(
                "border-red-500",
                "border-gray-300",
                "dark:border-gray-600"
            );
            input.classList.add("border-green-500");
            return;
        }

        input.classList.remove("border-red-500", "border-green-500");
        input.classList.add("border-gray-300", "dark:border-gray-600");
    }

    function validateInput(input) {
        const rule = input.dataset.validate;
        if (!rule || !validators[rule]) {
            return true;
        }

        const value =
            input.type === "file" ? input.files[0] || null : input.value || "";
        const result = validators[rule](value);

        if (result === true) {
            clearError(input);
            return true;
        }

        if (touched[input.id] || triedSubmit) {
            showError(input, result);
        } else {
            clearError(input);
        }

        return false;
    }

    function validateAll() {
        const inputs = form.querySelectorAll("[data-validate]");
        let isValid = true;

        inputs.forEach((input) => {
            if (!validateInput(input)) {
                isValid = false;
            }
        });

        const emailVerified =
            document.getElementById("email_verificado")?.value === "1";
        const emailValue = document
            .getElementById("email_contacto")
            ?.value.trim();

        if (!emailValue || !emailVerified) {
            isValid = false;
        }

        submitBtn.disabled = !isValid;
        return isValid;
    }

    form.querySelectorAll("[data-validate]").forEach((input) => {
        touched[input.id] = false;
        const eventName = input.type === "file" ? "change" : "input";

        input.addEventListener(eventName, () => {
            touched[input.id] = true;
            if (input.id === "dni") clearError(input);

            validateInput(input);
            validateAll();
        });

        input.addEventListener("blur", () => {
            touched[input.id] = true;
            validateInput(input);
            validateAll();
        });
    });

    const avatarInput = document.getElementById("avatar");
    const avatarPreview = document.getElementById("avatar-preview");
    const avatarPlaceholder = document.getElementById("avatar-placeholder");

    function previewImageInput(input) {
        const file = input.files && input.files[0];
        if (!file) {
            avatarPreview?.classList.add("hidden");
            avatarPlaceholder?.classList.remove("hidden");
            return;
        }

        const reader = new FileReader();
        reader.onload = (event) => {
            if (!avatarPreview || !avatarPlaceholder) return;
            avatarPreview.src = event.target?.result || "";
            avatarPreview.classList.remove("hidden");
            avatarPlaceholder.classList.add("hidden");
        };
        reader.readAsDataURL(file);
    }

    function setupAvatarDragAndDrop() {
        const dropZone = document.getElementById("avatar-drop-zone");
        const fileInput = document.getElementById("avatar");

        if (!dropZone || !fileInput) {
            return;
        }

        const preventDefaults = (event) => {
            event.preventDefault();
            event.stopPropagation();
        };

        const highlight = () => {
            dropZone.classList.add(
                "border-blue-500",
                "bg-blue-50",
                "dark:bg-blue-900/10",
                "dark:border-blue-400"
            );
            dropZone.classList.remove(
                "border-gray-300",
                "dark:border-gray-600"
            );
        };

        const unhighlight = () => {
            dropZone.classList.remove(
                "border-blue-500",
                "bg-blue-50",
                "dark:bg-blue-900/10",
                "dark:border-blue-400"
            );
            dropZone.classList.add("border-gray-300", "dark:border-gray-600");
        };

        const handleDrop = (event) => {
            const files = event.dataTransfer?.files;
            if (!files || files.length === 0) {
                return;
            }

            const file = files[0];
            const allowed = [
                "image/jpeg",
                "image/jpg",
                "image/png",
                "image/webp",
            ];

            if (!allowed.includes(file.type)) {
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                return;
            }

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;

            previewImageInput(fileInput);
            validateAll();
        };

        ["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        ["dragenter", "dragover"].forEach((eventName) => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ["dragleave", "drop"].forEach((eventName) => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        dropZone.addEventListener("drop", handleDrop, false);
    }

    window.previewImage = previewImageInput;

    if (avatarInput) {
        avatarInput.addEventListener("change", () => validateAll());
    }

    setupAvatarDragAndDrop();
    validateAll();

    form.addEventListener("submit", async (event) => {
        event.preventDefault();
        triedSubmit = true;

        const emailInput = document.getElementById("email_contacto");
        const emailVerified =
            document.getElementById("email_verificado")?.value === "1";

        if (!emailInput?.value.trim()) {
            emailInput?.focus();
            return;
        }

        if (!emailVerified) {
            emailInput?.focus();
            return;
        }

        if (!validateAll()) {
            const firstInvalid =
                form.querySelector("[data-validate].border-red-500") ||
                form.querySelector("[data-validate]");
            firstInvalid?.focus();
            return;
        }

        // Validación de DNI en el submit
        const dniInput = document.getElementById("dni");
        const dniValue = dniInput?.value.trim();
        const dniOk = await validateDniAvailability(dniValue, {
            decorateUI: true,
        });
        if (!dniOk) {
            dniInput?.focus();
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = `
			<span class="flex items-center justify-center">
				<svg class="animate-spin w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
				</svg>
				Guardando perfil...
			</span>
		`;

        form.submit();
    });

    setupEmailVerification({
        requiredFields: [
            { id: "primer_nombre", name: "Primer Nombre" },
            { id: "primer_apellido", name: "Primer Apellido" },
            { id: "dni", name: "DNI" },
            { id: "id_genero_fk", name: "Género" },
        ],
        onVerificationSuccess: validateAll,
        onVerificationReset: validateAll,
    });
});
