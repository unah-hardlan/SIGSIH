document.addEventListener("alpine:init", () => {
    Alpine.magic("responsive", () => ({
        isMobile() {
            return window.innerWidth < 768;
        },

        createCardData(item, fields) {
            const cardData = {};
            fields.forEach((field) => {
                if (typeof field === "string") {
                    cardData[field] = item[field];
                } else {
                    cardData[field.key] = {
                        value: item[field.key],
                        label: field.label,
                        format: field.format,
                    };
                }
            });
            return cardData;
        },

        formatValue(value, type = "text") {
            switch (type) {
                case "date":
                    return new Date(value).toLocaleDateString("es-ES");
                case "status":
                    return value ? "Activo" : "Inactivo";
                case "currency":
                    return new Intl.NumberFormat("es-HN", {
                        style: "currency",
                        currency: "HNL",
                    }).format(value);
                default:
                    return value;
            }
        },
    }));

    Alpine.data("tablaResponsive", (config = {}) => ({
        isMobile: window.innerWidth < 768,
        viewMode: "auto",
        searchTerm: "",
        sortField: "",
        sortDirection: "asc",

        init() {
            this.updateViewMode();
            window.addEventListener("resize", () => {
                this.updateViewMode();
            });
        },

        updateViewMode() {
            this.isMobile = window.innerWidth < 768;
            if (this.viewMode === "auto") {
                this.currentView = this.isMobile ? "cards" : "table";
            }
        },

        get currentView() {
            if (this.viewMode === "auto") {
                return this.isMobile ? "cards" : "table";
            }
            return this.viewMode;
        },

        toggleView() {
            this.viewMode = this.currentView === "table" ? "cards" : "table";
        },

        get filteredData() {
            let data = this.data || [];

            if (this.searchTerm) {
                data = data.filter((item) => {
                    return Object.values(item).some((value) =>
                        String(value)
                            .toLowerCase()
                            .includes(this.searchTerm.toLowerCase())
                    );
                });
            }

            if (this.sortField) {
                data.sort((a, b) => {
                    const aVal = a[this.sortField];
                    const bVal = b[this.sortField];

                    if (this.sortDirection === "asc") {
                        return aVal > bVal ? 1 : -1;
                    } else {
                        return aVal < bVal ? 1 : -1;
                    }
                });
            }

            return data;
        },

        sortBy(field) {
            if (this.sortField === field) {
                this.sortDirection =
                    this.sortDirection === "asc" ? "desc" : "asc";
            } else {
                this.sortField = field;
                this.sortDirection = "asc";
            }
        },
    }));
});

const style = document.createElement("style");
style.textContent = `
    .table-responsive-transition {
        transition: all 0.3s ease-in-out;
    }
    
    .mobile-card {
        transform: translateY(0);
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    
    .mobile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .mobile-card-enter {
        opacity: 0;
        transform: translateY(20px);
    }
    
    .mobile-card-enter-active {
        opacity: 1;
        transform: translateY(0);
        transition: opacity 0.3s ease-out, transform 0.3s ease-out;
    }
    
    @media (max-width: 767px) {
        .table-container {
            padding: 0.5rem;
        }
        
        .mobile-card {
            margin-bottom: 1rem;
        }
    }
`;
document.head.appendChild(style);
