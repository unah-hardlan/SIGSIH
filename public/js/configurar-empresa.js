document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("empresa-form");
    const submitBtn = document.getElementById("submit-btn");

    if (!form || !submitBtn) {
        return;
    }

    const validators = {
        name: (value) =>
            value.trim().length >= 2 || "Debe tener al menos 2 caracteres",
        rtn: (value) =>
            value.trim() === "" ||
            /^[0-9-]{6,20}$/.test(value.trim()) ||
            "RTN inválido (solo números y guiones, 6-20 caracteres)",
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

    const touched = {};
    let triedSubmit = false;

    function showError(input, message) {
        const el = document.querySelector(
            `[data-client-error-for="${input.id}"]`
        );
        if (el) {
            el.textContent = message;
            el.classList.remove("hidden");
        }
        input.classList.add("border-red-500");
        input.classList.remove("border-gray-300");
    }

    function clearError(input) {
        const el = document.querySelector(
            `[data-client-error-for="${input.id}"]`
        );
        if (el) {
            el.textContent = "";
            el.classList.add("hidden");
        }
        input.classList.remove("border-red-500");
        input.classList.add("border-gray-300");
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
            validateInput(input);
            validateAll();
        });

        input.addEventListener("blur", () => {
            touched[input.id] = true;
            validateInput(input);
            validateAll();
        });
    });

    form.addEventListener("submit", (event) => {
        triedSubmit = true;

        const emailInput = document.getElementById("email_contacto");
        const emailVerified =
            document.getElementById("email_verificado")?.value === "1";

        if (!emailInput?.value.trim()) {
            event.preventDefault();
            window.showToast?.("El email de contacto es obligatorio", "error");
            emailInput?.focus();
            return;
        }

        if (!emailVerified) {
            event.preventDefault();
            window.showToast?.(
                "Debe verificar el email de contacto antes de guardar",
                "error"
            );
            emailInput?.focus();
            return;
        }

        if (!validateAll()) {
            event.preventDefault();
            const firstInvalid =
                form.querySelector("[data-validate].border-red-500") ||
                form.querySelector("[data-validate]");
            firstInvalid?.focus();
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = `
			<span class="flex items-center justify-center">
				<svg class="animate-spin w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
				</svg>
				Guardando datos...
			</span>
		`;
    });

    function setupLogoDragAndDrop() {
        const dropZone = document.getElementById("logo-drop-zone");
        const fileInput = document.getElementById("avatar");

        if (!dropZone || !fileInput) {
            return;
        }

        const preventDefaults = (event) => {
            event.preventDefault();
            event.stopPropagation();
        };

        const highlight = () => {
            dropZone.classList.add("border-green-500", "bg-green-50");
            dropZone.classList.remove("border-gray-300");
        };

        const unhighlight = () => {
            dropZone.classList.remove("border-green-500", "bg-green-50");
            dropZone.classList.add("border-gray-300");
        };

        const handleDrop = (event) => {
            const files = event.dataTransfer?.files;
            if (!files || files.length === 0) {
                return;
            }

            const file = files[0];

            if (!file.type.match("image.*")) {
                window.showToast?.(
                    "Por favor selecciona solo archivos de imagen",
                    "warning"
                );
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                window.showToast?.(
                    "El archivo es muy grande. Máximo 5MB",
                    "error"
                );
                return;
            }

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;

            window.previewLogo?.(fileInput);
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

    function setupHorarioAtencion() {
        const hiddenInput = document.getElementById("horario_atencion");
        const dayCheckboxes = document.querySelectorAll("[data-day-checkbox]");
        const horarioInicio = document.getElementById("horario_inicio");
        const horarioFin = document.getElementById("horario_fin");
        const horarioPreview = document.getElementById("horario-preview");
        const presetButtons = document.querySelectorAll(".horario-preset");

        if (!hiddenInput || !horarioInicio || !horarioFin || !horarioPreview) {
            return;
        }

        const dayOrder = ["L", "M", "X", "J", "V", "S", "D"];

        const formatTime = (time24) => {
            const [hoursStr, minutes] = time24.split(":");
            let hours = parseInt(hoursStr, 10);
            const period = hours >= 12 ? "PM" : "AM";
            hours = hours % 12 || 12;
            return `${hours}:${minutes} ${period}`;
        };

        const updateHorario = () => {
            const selectedDays = Array.from(dayCheckboxes)
                .filter((checkbox) => checkbox.checked)
                .map((checkbox) => checkbox.value);

            const inicio = horarioInicio.value;
            const fin = horarioFin.value;

            if (selectedDays.length === 0 || !inicio || !fin) {
                horarioPreview.textContent = "—";
                hiddenInput.value = "";
                return;
            }

            const sortedDays = selectedDays.sort(
                (a, b) => dayOrder.indexOf(a) - dayOrder.indexOf(b)
            );
            let daysString = "";

            if (sortedDays.length === 7) {
                daysString = "L-D";
            } else if (
                sortedDays.length === 5 &&
                sortedDays.join("") === "LMXJV"
            ) {
                daysString = "L-V";
            } else if (
                sortedDays.length === 6 &&
                sortedDays.join("") === "LMXJVS"
            ) {
                daysString = "L-S";
            } else {
                const ranges = [];
                let start = 0;

                for (let i = 1; i <= sortedDays.length; i++) {
                    const isBreak =
                        i === sortedDays.length ||
                        dayOrder.indexOf(sortedDays[i]) -
                            dayOrder.indexOf(sortedDays[i - 1]) >
                            1;

                    if (isBreak) {
                        if (i - start > 2) {
                            ranges.push(
                                `${sortedDays[start]}-${sortedDays[i - 1]}`
                            );
                        } else {
                            for (let j = start; j < i; j++) {
                                ranges.push(sortedDays[j]);
                            }
                        }
                        start = i;
                    }
                }

                daysString = ranges.join(", ");
            }

            const horarioString = `${daysString} ${formatTime(
                inicio
            )}-${formatTime(fin)}`;
            horarioPreview.textContent = horarioString;
            hiddenInput.value = horarioString;
        };

        const convertTo24Hour = (time12) => {
            const match = time12.match(/(\d{1,2}):(\d{2})\s*(AM|PM)?/i);
            if (!match) return "08:00";

            let [, hours, minutes, period] = match;
            let h = parseInt(hours, 10);

            if (period && period.toUpperCase() === "PM" && h !== 12) {
                h += 12;
            } else if (period && period.toUpperCase() === "AM" && h === 12) {
                h = 0;
            }

            return `${h.toString().padStart(2, "0")}:${minutes}`;
        };

        presetButtons.forEach((button) => {
            button.addEventListener("click", () => {
                const preset = button.dataset.preset;

                dayCheckboxes.forEach((checkbox) => {
                    if (preset === "weekdays") {
                        checkbox.checked = ["L", "M", "X", "J", "V"].includes(
                            checkbox.value
                        );
                    } else if (preset === "weekends") {
                        checkbox.checked = [
                            "L",
                            "M",
                            "X",
                            "J",
                            "V",
                            "S",
                        ].includes(checkbox.value);
                    } else if (preset === "all") {
                        checkbox.checked = true;
                    } else if (preset === "none") {
                        checkbox.checked = false;
                    }
                });

                updateHorario();
            });
        });

        dayCheckboxes.forEach((checkbox) =>
            checkbox.addEventListener("change", updateHorario)
        );
        horarioInicio.addEventListener("change", updateHorario);
        horarioFin.addEventListener("change", updateHorario);

        const existingValue = hiddenInput.value;
        if (existingValue) {
            const match = existingValue.match(
                /^([LMXJVSD, -]+)\s+(\d{1,2}:\d{2}\s*(?:AM|PM)?)-(\d{1,2}:\d{2}\s*(?:AM|PM)?)$/i
            );
            if (match) {
                const [, daysStr, inicioStr, finStr] = match;

                dayCheckboxes.forEach((checkbox) => {
                    checkbox.checked = false;
                });

                if (daysStr.includes("-")) {
                    const [startDay, endDay] = daysStr
                        .split("-")
                        .map((day) => day.trim());
                    const startIndex = dayOrder.indexOf(startDay);
                    const endIndex = dayOrder.indexOf(endDay);

                    if (startIndex !== -1 && endIndex !== -1) {
                        for (let i = startIndex; i <= endIndex; i++) {
                            const checkbox = Array.from(dayCheckboxes).find(
                                (cb) => cb.value === dayOrder[i]
                            );
                            if (checkbox) checkbox.checked = true;
                        }
                    }
                } else {
                    daysStr.split(",").forEach((day) => {
                        const checkbox = Array.from(dayCheckboxes).find(
                            (cb) => cb.value === day.trim()
                        );
                        if (checkbox) checkbox.checked = true;
                    });
                }

                horarioInicio.value = convertTo24Hour(inicioStr.trim());
                horarioFin.value = convertTo24Hour(finStr.trim());
            }
        }

        updateHorario();
    }

    function previewLogoInput(input) {
        const preview = document.getElementById("logo-preview");
        const placeholder = document.getElementById("logo-placeholder");
        const file = input.files && input.files[0];

        if (!preview || !placeholder) {
            return;
        }

        if (!file) {
            preview.src = "";
            preview.classList.add("hidden");
            placeholder.classList.remove("hidden");
            return;
        }

        const reader = new FileReader();
        reader.onload = (event) => {
            preview.src = event.target?.result || "";
            preview.classList.remove("hidden");
            placeholder.classList.add("hidden");
        };
        reader.readAsDataURL(file);
    }

    window.previewLogo = previewLogoInput;

    setupLogoDragAndDrop();
    setupHorarioAtencion();
    validateAll();

    setupEmailVerification({
        requiredFields: [{ id: "nombre_comercial", name: "Nombre Comercial" }],
        onVerificationSuccess: validateAll,
        onVerificationReset: validateAll,
    });
});
