// Simple client-side helper to provide subdivisions (departamentos/estados)
// for selected countries. Start with Honduras and easily extend later.
// Exposes a global: window.subdivisionHelper.getByPaisName(nombrePais)

(function () {
    const DATA = {
        Honduras: [
            "Atlántida",
            "Choluteca",
            "Colón",
            "Comayagua",
            "Copán",
            "Cortés",
            "El Paraíso",
            "Francisco Morazán",
            "Gracias a Dios",
            "Intibucá",
            "Islas de la Bahía",
            "La Paz",
            "Lempira",
            "Ocotepeque",
            "Olancho",
            "Santa Bárbara",
            "Valle",
            "Yoro",
        ],
        Guatemala: [
            "Alta Verapaz",
            "Baja Verapaz",
            "Chimaltenango",
            "Chiquimula",
            "El Progreso",
            "Escuintla",
            "Guatemala",
            "Huehuetenango",
            "Izabal",
            "Jalapa",
            "Jutiapa",
            "Petén",
            "Quetzaltenango",
            "Quiché",
            "Retalhuleu",
            "Sacatepéquez",
            "San Marcos",
            "Santa Rosa",
            "Sololá",
            "Suchitepéquez",
            "Totonicapán",
            "Zacapa",
        ],
        Nicaragua: [
            "Boaco",
            "Carazo",
            "Chinandega",
            "Chontales",
            "Estelí",
            "Granada",
            "Jinotega",
            "León",
            "Madriz",
            "Managua",
            "Masaya",
            "Matagalpa",
            "Nueva Segovia",
            "Rivas",
            "Río San Juan",
            "RACCN",
            "RACCS",
        ],
        "El Salvador": [
            "Ahuachapán",
            "Cabañas",
            "Chalatenango",
            "Cuscatlán",
            "La Libertad",
            "La Paz",
            "La Unión",
            "Morazán",
            "San Miguel",
            "San Salvador",
            "San Vicente",
            "Santa Ana",
            "Sonsonate",
            "Usulután",
        ],
        "Costa Rica": [
            "San José",
            "Alajuela",
            "Cartago",
            "Heredia",
            "Guanacaste",
            "Puntarenas",
            "Limón",
        ],
    };

    // Optional city catalog by Country -> Department -> Cities
    const CITIES = {
        Honduras: {
            "Atlántida": ["La Ceiba", "Tela", "Jutiapa"],
            "Choluteca": ["Choluteca", "San Marcos de Colón"],
            "Comayagua": ["Comayagua", "Siguatepeque", "La Libertad"],
            "Cortés": [
                "San Pedro Sula",
                "Puerto Cortés",
                "Choloma",
                "La Lima",
                "Villanueva",
            ],
            "Francisco Morazán": [
                "Tegucigalpa",
                "Comayagüela",
                "Valle de Ángeles",
                "Santa Lucía",
                "Talanga",
            ],
            "Yoro": ["El Progreso", "Yoro", "Olanchito"],
            "Olancho": ["Juticalpa", "Catacamas"],
            "Santa Bárbara": ["Santa Bárbara", "Quimistán"],
            "La Paz": ["La Paz", "Marcala"],
            "Copán": ["Santa Rosa de Copán", "Copán Ruinas"],
            "El Paraíso": ["Yuscarán", "Danlí"],
            "Islas de la Bahía": ["Roatán", "Utila", "Guanaja"],
            "Valle": ["Nacaome", "San Lorenzo"],
            "Colón": ["Trujillo", "Tocoa"],
            "Intibucá": ["La Esperanza", "Intibucá"],
            "Ocotepeque": ["Ocotepeque", "Santa Fe"],
            "Gracias a Dios": ["Puerto Lempira"],
            "Lempira": ["Gracias"],
        },
    };

    function normalize(str) {
        return (str || "")
            .toString()
            .trim()
            .toLowerCase()
            .normalize("NFD")
            .replace(/\p{Diacritic}/gu, "");
    }

    function getByPaisName(nombrePais) {
        if (!nombrePais) return [];
        // First try exact match
        if (DATA[nombrePais]) return DATA[nombrePais].slice();
        // Then try normalized match
        const n = normalize(nombrePais);
        for (const key of Object.keys(DATA)) {
            if (normalize(key) === n) return DATA[key].slice();
        }
        return [];
    }

    function getCitiesByPaisDep(nombrePais, nombreDepartamento) {
        if (!nombrePais || !nombreDepartamento) return [];
        const find = (obj, key) => {
            if (obj[key]) return obj[key];
            const n = normalize(key);
            for (const k of Object.keys(obj)) {
                if (normalize(k) === n) return obj[k];
            }
            return null;
        };
        const countries = CITIES || {};
        const byPais = find(countries, nombrePais);
        if (!byPais) return [];
        const byDep = find(byPais, nombreDepartamento);
        return Array.isArray(byDep) ? byDep.slice() : [];
    }

    try {
        window.subdivisionHelper = {
            getByPaisName,
            getCitiesByPaisDep,
            listCountries() {
                return Object.keys(DATA).slice();
            },
        };
    } catch (_) { }
})();
