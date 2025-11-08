export async function getCached(
    url,
    { ttlMs = 2 * 60 * 1000, force = false, mapper } = {}
) {
    const now = Date.now();
    const cacheUrl = `/cache?url=${encodeURIComponent(
        url
    )}&ttl=${ttlMs}&force=${force}`;

    try {
        const response = await fetch(cacheUrl, {
            method: "GET",
            headers: { Accept: "application/json" },
            credentials: "same-origin",
        });

        if (!response.ok) {
            throw new Error(
                `Failed to fetch cached data: ${response.statusText}`
            );
        }

        const cachedData = await response.json();
        if (mapper && typeof mapper === "function") {
            return mapper(cachedData);
        }

        return cachedData;
    } catch (error) {
        console.error("Error fetching cached data:", error);
        throw error;
    }
}

export function invalidate(url) {
    store.del(`GET:${url}`);
}
