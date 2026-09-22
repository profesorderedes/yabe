import { useEffect, useState } from 'react';

function HotelSelect({ value, onChange, hotels, loading, error }) {
    if (loading) {
        return (
            <div className="text-sm text-gray-500">Cargando hoteles…</div>
        );
    }

    if (error) {
        return (
            <div className="text-sm text-red-600">
                No se han podido cargar los hoteles: {error}
            </div>
        );
    }

    return (
        <select
            id="hotel"
            name="hotel"
            value={value}
            onChange={(event) => onChange(event.target.value)}
            className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
        >
            <option value="">Todos los hoteles</option>
            {hotels.map((hotel) => (
                <option key={hotel.code} value={hotel.code}>
                    {hotel.name}
                </option>
            ))}
        </select>
    );
}

function AvailabilityResults({ results, checked }) {
    if (!checked) {
        return null;
    }

    if (results.length === 0) {
        return (
            <p className="mt-6 text-center text-gray-500">
                No hay habitaciones disponibles para los datos introducidos.
            </p>
        );
    }

    return (
        <ul className="mt-6 space-y-3">
            {results.map((result) => (
                <li
                    key={`${result.hotel.code}-${result.roomType.code}`}
                    className="flex items-center justify-between rounded-md border border-gray-200 bg-white px-4 py-3"
                >
                    <div>
                        <p className="font-medium text-gray-900">{result.hotel.name}</p>
                        <p className="text-sm text-gray-600">{result.roomType.name}</p>
                    </div>
                    <p className="text-lg font-semibold text-gray-900">
                        {formatPrice(result.price)}
                    </p>
                </li>
            ))}
        </ul>
    );
}

function formatPrice(price) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
    }).format(price);
}

function ErrorMessage({ message }) {
    if (!message) {
        return null;
    }

    return (
        <div className="mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {message}
        </div>
    );
}

function LoadingBanner({ loading, label }) {
    if (!loading) {
        return null;
    }

    return (
        <div className="mt-4 flex items-center gap-2 rounded-md border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
            <span className="inline-block h-4 w-4 animate-spin rounded-full border-2 border-blue-700 border-t-transparent" />
            {label}
        </div>
    );
}

function App() {
    const [hotels, setHotels] = useState([]);
    const [hotelsError, setHotelsError] = useState(null);
    const [hotelsLoading, setHotelsLoading] = useState(true);

    const [checkin, setCheckin] = useState('');
    const [checkout, setCheckout] = useState('');
    const [paxes, setPaxes] = useState(2);
    const [hotel, setHotel] = useState('');

    const [results, setResults] = useState([]);
    const [checked, setChecked] = useState(false);
    const [checking, setChecking] = useState(false);
    const [checkError, setCheckError] = useState(null);

    useEffect(() => {
        fetch('/api/v1/hotels', { headers: { Accept: 'application/json' } })
            .then((response) => {
                if (!response.ok) {
                    return response.json().then((data) => {
                        throw new Error(data.message ?? `Error ${response.status}`);
                    });
                }
                return response.json();
            })
            .then((data) => {
                setHotels(data);
            })
            .catch((error) => {
                setHotelsError(error.message);
            })
            .finally(() => {
                setHotelsLoading(false);
            });
    }, []);

    function handleSubmit(event) {
        event.preventDefault();
        setChecking(true);
        setCheckError(null);

        const payload = {
            checkin,
            checkout,
            paxes: Number(paxes),
        };

        if (hotel !== '') {
            payload.hotel = hotel;
        }

        fetch('/api/v1/availability', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload),
        })
            .then((response) => {
                if (!response.ok) {
                    return response.json().then((data) => {
                        throw new Error(data.message ?? `Error ${response.status}`);
                    });
                }
                return response.json();
            })
            .then((data) => {
                setResults(data);
                setChecked(true);
            })
            .catch((error) => {
                setCheckError(error.message);
                setChecked(false);
            })
            .finally(() => {
                setChecking(false);
            });
    }

    return (
        <div className="w-full">
            <h1 className="text-2xl font-semibold text-gray-900">
                Consultar disponibilidad
            </h1>
            <p className="mt-1 text-gray-600">
                Introduce las fechas y el número de huéspedes para consultar las
                habitaciones disponibles.
            </p>

            <form
                onSubmit={handleSubmit}
                className="mt-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
            >
                <div className="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label
                            htmlFor="checkin"
                            className="block text-sm font-medium text-gray-700"
                        >
                            Fecha de entrada
                        </label>
                        <input
                            id="checkin"
                            name="checkin"
                            type="date"
                            required
                            value={checkin}
                            onChange={(event) => setCheckin(event.target.value)}
                            className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label
                            htmlFor="checkout"
                            className="block text-sm font-medium text-gray-700"
                        >
                            Fecha de salida
                        </label>
                        <input
                            id="checkout"
                            name="checkout"
                            type="date"
                            required
                            value={checkout}
                            onChange={(event) => setCheckout(event.target.value)}
                            className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label
                            htmlFor="paxes"
                            className="block text-sm font-medium text-gray-700"
                        >
                            Número de huéspedes
                        </label>
                        <input
                            id="paxes"
                            name="paxes"
                            type="number"
                            min="1"
                            required
                            value={paxes}
                            onChange={(event) => setPaxes(event.target.value)}
                            className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label
                            htmlFor="hotel"
                            className="block text-sm font-medium text-gray-700"
                        >
                            Hotel
                        </label>
                        <HotelSelect
                            value={hotel}
                            onChange={setHotel}
                            hotels={hotels}
                            loading={hotelsLoading}
                            error={hotelsError}
                        />
                    </div>
                </div>

                <div className="mt-6 flex items-center justify-between">
                    <p className="text-xs text-gray-500">
                        Seleccionar un hotel es opcional.
                    </p>
                    <button
                        type="submit"
                        disabled={checking}
                        className="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {checking ? 'Consultando…' : 'Consultar disponibilidad'}
                    </button>
                </div>

                <LoadingBanner
                    loading={checking}
                    label="Consultando disponibilidad…"
                />
                <ErrorMessage message={checkError} />
            </form>

            <AvailabilityResults results={results} checked={checked} />
        </div>
    );
}

export default App;