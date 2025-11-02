(() => {
    const DEFAULT_ENDPOINTS = {
        send: "/api/email-contacto/enviar-codigo",
        verify: "/api/email-contacto/verificar-codigo",
    };

    const noop = () => {};

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || "";
    }

    function getToastFn() {
        return typeof window.showToast === "function"
            ? window.showToast
            : (message) => console.warn("[toast]", message);
    }

    function setupEmailVerification(options = {}) {
        const config = {
            emailInputId: "email_contacto",
            sendButtonId: "btn-enviar-codigo",
            verifyButtonId: "btn-verificar-codigo",
            verificationSectionId: "verification-section",
            verificationSuccessId: "verification-success",
            verificationErrorId: "verification-error",
            verificationInputId: "codigo_verificacion",
            verifiedFieldId: "email_verificado",
            submitButtonId: null,
            requiredFields: [],
            endpoints: DEFAULT_ENDPOINTS,
            resetDelay: 3000,
            onVerificationSuccess: noop,
            onVerificationReset: noop,
            onSendSuccess: noop,
            onSendError: noop,
            onVerifyError: noop,
            ...options,
        };

        const emailInput = document.getElementById(config.emailInputId);
        const sendButton = document.getElementById(config.sendButtonId);
        const verifyButton = document.getElementById(config.verifyButtonId);
        const verificationSection = document.getElementById(
            config.verificationSectionId
        );
        const verificationSuccess = document.getElementById(
            config.verificationSuccessId
        );
        const verificationError = document.getElementById(
            config.verificationErrorId
        );
        const verificationInput = document.getElementById(
            config.verificationInputId
        );
        const verifiedField = document.getElementById(config.verifiedFieldId);
        const submitButton = config.submitButtonId
            ? document.getElementById(config.submitButtonId)
            : null;

        if (
            !emailInput ||
            !sendButton ||
            !verifyButton ||
            !verificationSection ||
            !verificationSuccess ||
            !verificationError ||
            !verificationInput ||
            !verifiedField
        ) {
            return;
        }

        const toast = getToastFn();
        const csrfToken = getCsrfToken();

        const state = {
            attemptsRemaining: 3,
            isVerifying: false,
        };

        function setSendButtonLoading(loading) {
            sendButton.disabled = loading;
            sendButton.textContent = loading ? "Enviando..." : "Enviar Código";
        }

        function setVerifyButtonLoading(loading) {
            verifyButton.disabled = loading;
            verifyButton.textContent = loading ? "Verificando..." : "Verificar";
        }

        function showVerificationError(message) {
            verificationError.textContent = message;
            verificationError.classList.remove("hidden");
        }

        function clearVerificationError() {
            verificationError.textContent = "";
            verificationError.classList.add("hidden");
        }

        function markEmailVerified() {
            verificationSection.classList.add("hidden");
            verificationSuccess.classList.remove("hidden");
            verificationSuccess.classList.add("flex");
            verifiedField.value = "1";
            config.onVerificationSuccess();
        }

        function resetVerification({ keepReadonly = false } = {}) {
            verificationSection.classList.add("hidden");
            verificationSuccess.classList.add("hidden");
            verificationSuccess.classList.remove("flex");
            verificationInput.value = "";
            verificationInput.disabled = false;
            verifyButton.disabled = false;
            verifyButton.textContent = "Verificar";
            setSendButtonLoading(false);
            clearVerificationError();
            verifiedField.value = "0";
            state.attemptsRemaining = 3;
            if (!keepReadonly) {
                emailInput.readOnly = false;
            }
            config.onVerificationReset();
        }

        function validateRequiredFields() {
            const missing = [];
            config.requiredFields.forEach((field) => {
                const input = document.getElementById(field.id);
                if (!input) return;

                const value = input.value?.trim();
                if (!value) {
                    missing.push(field.name);
                    input.classList.add("border-red-500");
                    input.classList.remove("border-gray-300");
                } else {
                    input.classList.remove("border-red-500");
                    input.classList.add("border-gray-300");
                }
            });

            if (missing.length > 0) {
                const firstMissing = config.requiredFields.find((field) =>
                    missing.includes(field.name)
                );
                if (firstMissing) {
                    document.getElementById(firstMissing.id)?.focus();
                }
                toast(
                    `Debe completar los siguientes campos antes del email de contacto: ${missing.join(
                        ", "
                    )}`,
                    "warning"
                );
                return false;
            }

            return true;
        }

        async function handleSendCode() {
            if (!validateRequiredFields()) {
                return;
            }

            const email = emailInput.value.trim();
            if (!email || !emailInput.checkValidity()) {
                toast("Por favor, ingrese un email válido", "warning");
                emailInput.focus();
                return;
            }

            setSendButtonLoading(true);
            clearVerificationError();

            try {
                const response = await fetch(config.endpoints.send, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({ email }),
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(
                        data.message || "Error al enviar el código"
                    );
                }

                verificationSection.classList.remove("hidden");
                verificationSuccess.classList.add("hidden");
                verificationError.classList.add("hidden");
                emailInput.readOnly = true;
                verificationInput.focus();
                sendButton.disabled = true;
                sendButton.textContent = "Código Enviado";
                state.attemptsRemaining = 3;
                config.onSendSuccess(data);
            } catch (error) {
                console.error("Email verification send error:", error);
                toast(
                    error.message ||
                        "Error al enviar el código. Por favor, intenta nuevamente.",
                    "error"
                );
                setSendButtonLoading(false);
                config.onSendError(error);
            }
        }

        async function handleVerifyCode() {
            const codigo = verificationInput.value.trim();
            const email = emailInput.value.trim();

            if (codigo.length !== 6) {
                showVerificationError(
                    "Por favor, ingrese el código de 6 dígitos"
                );
                return;
            }

            setVerifyButtonLoading(true);
            clearVerificationError();

            try {
                const response = await fetch(config.endpoints.verify, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({ email, codigo }),
                });

                const data = await response.json();

                if (response.ok && data.success && data.verified) {
                    markEmailVerified();
                    setVerifyButtonLoading(false);
                    return;
                }

                const message = data.message || "Código incorrecto";
                showVerificationError(message);

                const attemptsRemaining = data.attempts_remaining;
                state.attemptsRemaining =
                    typeof attemptsRemaining === "number"
                        ? attemptsRemaining
                        : state.attemptsRemaining - 1;

                if (state.attemptsRemaining <= 0 || response.status === 429) {
                    verificationInput.disabled = true;
                    verifyButton.disabled = true;
                    setTimeout(
                        () => resetVerification({ keepReadonly: false }),
                        config.resetDelay
                    );
                } else {
                    verificationInput.value = "";
                    verificationInput.focus();
                    setVerifyButtonLoading(false);
                }

                config.onVerifyError(data);
            } catch (error) {
                console.error("Email verification check error:", error);
                showVerificationError(
                    "Error al verificar el código. Por favor, intenta nuevamente."
                );
                setVerifyButtonLoading(false);
                config.onVerifyError(error);
            }
        }

        sendButton.addEventListener("click", handleSendCode);
        verifyButton.addEventListener("click", handleVerifyCode);

        verificationInput.addEventListener("keypress", (event) => {
            if (event.key === "Enter") {
                event.preventDefault();
                verifyButton.click();
            }
        });

        verificationInput.addEventListener("input", function () {
            this.value = this.value.replace(/[^0-9]/g, "");
        });

        emailInput.addEventListener("input", () => {
            if (verifiedField.value === "1") {
                resetVerification({ keepReadonly: false });
            }
        });

        if (submitButton) {
            submitButton.disabled = verifiedField.value !== "1";
        }

        return {
            reset: resetVerification,
        };
    }

    window.setupEmailVerification = setupEmailVerification;
})();
