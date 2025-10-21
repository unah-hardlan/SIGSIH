(function () {
    // Catálogo de subdivisiones por país (Centroamérica)
    const DATA = {
        Honduras: [
            "Atlántida","Choluteca","Colón","Comayagua","Copán","Cortés","El Paraíso",
            "Francisco Morazán","Gracias a Dios","Intibucá","Islas de la Bahía","La Paz",
            "Lempira","Ocotepeque","Olancho","Santa Bárbara","Valle","Yoro",
        ],
        Guatemala: [
            "Alta Verapaz","Baja Verapaz","Chimaltenango","Chiquimula","El Progreso","Escuintla",
            "Guatemala","Huehuetenango","Izabal","Jalapa","Jutiapa","Petén","Quetzaltenango",
            "Quiché","Retalhuleu","Sacatepéquez","San Marcos","Santa Rosa","Sololá",
            "Suchitepéquez","Totonicapán","Zacapa",
        ],
        Nicaragua: [
            "Boaco","Carazo","Chinandega","Chontales","Estelí","Granada","Jinotega","León",
            "Madriz","Managua","Masaya","Matagalpa","Nueva Segovia","Rivas","Río San Juan",
            "RACCN","RACCS",
        ],
        "El Salvador": [
            "Ahuachapán","Cabañas","Chalatenango","Cuscatlán","La Libertad","La Paz","La Unión",
            "Morazán","San Miguel","San Salvador","San Vicente","Santa Ana","Sonsonate","Usulután",
        ],
        "Costa Rica": ["San José","Alajuela","Cartago","Heredia","Guanacaste","Puntarenas","Limón"],
        "Panamá": [
            "Bocas del Toro","Chiriquí","Coclé","Colón","Darién","Herrera","Los Santos","Panamá",
            "Panamá Oeste","Veraguas","Guna Yala","Ngäbe-Buglé","Emberá-Wounaan",
            "Guna de Madungandí","Guna de Wargandí","Naso Tjër Di",
        ],
        // Alias sin tilde para coincidencias flexibles
        Panama: [
            "Bocas del Toro","Chiriqui","Cocle","Colon","Darien","Herrera","Los Santos","Panama",
            "Panama Oeste","Veraguas","Guna Yala","Ngabe-Bugle","Embera-Wounaan",
            "Guna de Madungandi","Guna de Wargandi","Naso Tjer Di",
        ],
        Belice: ["Corozal","Orange Walk","Belize","Cayo","Stann Creek","Toledo"],
        Belize: ["Corozal","Orange Walk","Belize","Cayo","Stann Creek","Toledo"],
    };

    // Catálogo opcional de ciudades por País -> Departamento/Provincia -> Ciudades (principales)
    const CITIES = {
        Honduras: {
            "Atlántida": ["La Ceiba","Tela","Jutiapa"],
            "Choluteca": ["Choluteca","San Marcos de Colón"],
            "Colón": ["Trujillo","Tocoa","Sonaguera"],
            "Comayagua": ["Comayagua","Siguatepeque","La Libertad"],
            "Copán": ["Santa Rosa de Copán","Copán Ruinas","Dulce Nombre"],
            "Cortés": ["San Pedro Sula","Puerto Cortés","Choloma","La Lima","Villanueva"],
            "El Paraíso": ["Yuscarán","Danlí","El Paraíso"],
            "Francisco Morazán": ["Tegucigalpa","Comayagüela","Valle de Ángeles","Santa Lucía","Talanga"],
            "Gracias a Dios": ["Puerto Lempira","Ahuas"],
            "Intibucá": ["La Esperanza","Intibucá"],
            "Islas de la Bahía": ["Roatán","Utila","Guanaja"],
            "La Paz": ["La Paz","Marcala"],
            "Lempira": ["Gracias","Erandique"],
            "Ocotepeque": ["Ocotepeque","Santa Fe"],
            "Olancho": ["Juticalpa","Catacamas"],
            "Santa Bárbara": ["Santa Bárbara","Quimistán","Nueva Frontera"],
            "Valle": ["Nacaome","San Lorenzo"],
            "Yoro": ["El Progreso","Yoro","Olanchito"],
        },
        Guatemala: {
            "Alta Verapaz": ["Cobán","San Pedro Carchá","Tactic"],
            "Baja Verapaz": ["Salamá","Rabinal","Cubulco"],
            "Chimaltenango": ["Chimaltenango","San José Poaquil","San Martín Jilotepeque"],
            "Chiquimula": ["Chiquimula","Esquipulas","Jocotán"],
            "El Progreso": ["Guastatoya","Morazán","San Agustín Acasaguastlán"],
            "Escuintla": ["Escuintla","Santa Lucía Cotzumalguapa","Puerto San José"],
            "Guatemala": ["Ciudad de Guatemala","Mixco","Villa Nueva"],
            "Huehuetenango": ["Huehuetenango","Jacaltenango","La Democracia"],
            "Izabal": ["Puerto Barrios","Morales","Livingston"],
            "Jalapa": ["Jalapa","San Pedro Pinula","Monjas"],
            "Jutiapa": ["Jutiapa","Achuapa","Asunción Mita"],
            "Petén": ["Flores","San Benito","Sayaxché"],
            "Quetzaltenango": ["Quetzaltenango","Olintepeque","Salcajá"],
            "Quiché": ["Santa Cruz del Quiché","Nebaj","Chichicastenango"],
            "Retalhuleu": ["Retalhuleu","San Sebastián","San Martín Zapotitlán"],
            "Sacatepéquez": ["Antigua Guatemala","Ciudad Vieja","San Lucas Sacatepéquez"],
            "San Marcos": ["San Marcos","Malacatán","Ayutla (Tecún Umán)"],
            "Santa Rosa": ["Cuilapa","Barberena","Guazacapán"],
            "Sololá": ["Sololá","Panajachel","Santiago Atitlán"],
            "Suchitepéquez": ["Mazatenango","Cuyotenango","San Bernardino"],
            "Totonicapán": ["Totonicapán","Momostenango","San Cristóbal Totonicapán"],
            "Zacapa": ["Zacapa","Río Hondo","Gualán"],
        },
        "El Salvador": {
            "Ahuachapán": ["Ahuachapán","Apaneca","Atiquizaya"],
            "Cabañas": ["Sensuntepeque","Ilobasco","Victoria"],
            "Chalatenango": ["Chalatenango","La Palma","Nueva Concepción"],
            "Cuscatlán": ["Cojutepeque","Suchitoto","San Pedro Perulapán"],
            "La Libertad": ["Santa Tecla","Antiguo Cuscatlán","Nuevo Cuscatlán"],
            "La Paz": ["Zacatecoluca","Olocuilta","San Luis Talpa"],
            "La Unión": ["La Unión","Santa Rosa de Lima","Conchagua"],
            "Morazán": ["San Francisco Gotera","Jocoro","Perquín"],
            "San Miguel": ["San Miguel","Chinameca","Moncagua"],
            "San Salvador": ["San Salvador","Soyapango","Mejicanos","Ilopango"],
            "San Vicente": ["San Vicente","Apastepeque","Tecoluca"],
            "Santa Ana": ["Santa Ana","Metapán","Chalchuapa"],
            "Sonsonate": ["Sonsonate","Acajutla","Nahuilingo"],
            "Usulután": ["Usulután","Jiquilisco","Santiago de María"],
        },
        Nicaragua: {
            "Boaco": ["Boaco","Camoapa","San Lorenzo"],
            "Carazo": ["Jinotepe","Diriamba","San Marcos"],
            "Chinandega": ["Chinandega","El Viejo","Corinto"],
            "Chontales": ["Juigalpa","Santo Tomás","Acoyapa"],
            "Estelí": ["Estelí","Condega","Pueblo Nuevo"],
            "Granada": ["Granada","Nandaime","Diriá"],
            "Jinotega": ["Jinotega","San Rafael del Norte","Santa María de Pantasma"],
            "León": ["León","Nagarote","La Paz Centro"],
            "Madriz": ["Somoto","Yalagüina","Palacagüina"],
            "Managua": ["Managua","Tipitapa","Ciudad Sandino"],
            "Masaya": ["Masaya","Nindirí","Ticuantepe"],
            "Matagalpa": ["Matagalpa","Sébaco","Río Blanco"],
            "Nueva Segovia": ["Ocotal","Jalapa","Dipilto"],
            "Rivas": ["Rivas","San Juan del Sur","Tola"],
            "Río San Juan": ["San Carlos","El Castillo","San Miguelito"],
            RACCN: ["Bilwi (Puerto Cabezas)","Waspam","Rosita"],
            RACCS: ["Bluefields","Laguna de Perlas","Kukra Hill"],
        },
        "Costa Rica": {
            "San José": ["San José","Desamparados","Escazú"],
            "Alajuela": ["Alajuela","San Ramón","Grecia"],
            "Cartago": ["Cartago","Turrialba","Paraíso"],
            "Heredia": ["Heredia","Santo Domingo","San Isidro"],
            "Guanacaste": ["Liberia","Nicoya","Santa Cruz"],
            "Puntarenas": ["Puntarenas","Quepos","Buenos Aires"],
            "Limón": ["Limón","Guápiles","Siquirres"],
        },
        "Panamá": {
            "Bocas del Toro": ["Bocas del Toro (Isla Colón)","Changuinola","Almirante"],
            "Chiriquí": ["David","Boquete","Bugaba"],
            "Coclé": ["Penonomé","Aguadulce","Antón"],
            "Colón": ["Colón","Portobelo","Sabanitas"],
            "Darién": ["La Palma","Yaviza","Garachiné"],
            "Herrera": ["Chitré","Parita","Las Minas"],
            "Los Santos": ["Las Tablas","Guararé","Pedasí"],
            "Panamá": ["Ciudad de Panamá","San Miguelito","Tocumen"],
            "Panamá Oeste": ["La Chorrera","Arraiján","Capira"],
            "Veraguas": ["Santiago","Soná","Atalaya"],
            "Guna Yala": ["El Porvenir","Cartí","Playón Chico"],
            "Ngäbe-Buglé": ["Llano Tugrí (Buäbidí)","Kankintú","Chichica"],
            "Emberá-Wounaan": ["Unión Chocó","Sambú","Cirilo Guainora"],
            "Guna de Madungandí": ["Aguas Claras","Ibedí","Ipetí Guna"],
            "Guna de Wargandí": ["Mortí","Nurra","Wala"],
            "Naso Tjër Di": ["Sieyik","Bonyic","Sieykin"],
        },
        Panama: { // alias sin tilde
            "Bocas del Toro": ["Bocas del Toro (Isla Colon)","Changuinola","Almirante"],
            Chiriqui: ["David","Boquete","Bugaba"],
            Cocle: ["Penonome","Aguadulce","Anton"],
            Colon: ["Colon","Portobelo","Sabanitas"],
            Darien: ["La Palma","Yaviza","Garachine"],
            Herrera: ["Chitre","Parita","Las Minas"],
            "Los Santos": ["Las Tablas","Guarare","Pedasi"],
            Panama: ["Ciudad de Panama","San Miguelito","Tocumen"],
            "Panama Oeste": ["La Chorrera","Arraijan","Capira"],
            Veraguas: ["Santiago","Sona","Atalaya"],
            "Guna Yala": ["El Porvenir","Carti","Playon Chico"],
            "Ngabe-Bugle": ["Llano Tugri","Kankintu","Chichica"],
            "Embera-Wounaan": ["Union Choco","Sambu","Cirilo Guainora"],
            "Guna de Madungandi": ["Aguas Claras","Ibedi","Ipety Guna"],
            "Guna de Wargandi": ["Morti","Nurra","Wala"],
            "Naso Tjer Di": ["Sieyik","Bonyic","Sieykin"],
        },
        Belice: {
            Corozal: ["Corozal Town","Consejo Shores","Sarteneja"],
            "Orange Walk": ["Orange Walk Town","Trial Farm","Shipyard"],
            Belize: ["Belize City","San Pedro","Ladyville"],
            Cayo: ["Belmopan","San Ignacio","Santa Elena"],
            "Stann Creek": ["Dangriga","Placencia","Hopkins"],
            Toledo: ["Punta Gorda","Big Falls","San Antonio"],
        },
        Belize: { // alias en inglés
            Corozal: ["Corozal Town","Consejo Shores","Sarteneja"],
            "Orange Walk": ["Orange Walk Town","Trial Farm","Shipyard"],
            Belize: ["Belize City","San Pedro","Ladyville"],
            Cayo: ["Belmopan","San Ignacio","Santa Elena"],
            "Stann Creek": ["Dangriga","Placencia","Hopkins"],
            Toledo: ["Punta Gorda","Big Falls","San Antonio"],
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
        if (DATA[nombrePais]) return DATA[nombrePais].slice();
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
        const byPais = find(CITIES, nombrePais);
        if (!byPais) return [];
        const byDep = find(byPais, nombreDepartamento);
        return Array.isArray(byDep) ? byDep.slice() : [];
    }

    try {
        window.subdivisionHelper = {
            getByPaisName,
            getCitiesByPaisDep,
            listCountries() { return Object.keys(DATA).slice(); },
        };
    } catch (_) {}
})();
