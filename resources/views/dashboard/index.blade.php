@extends('layout.template')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Dashboard</h1>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @else
        <div class="row mb-4">
        <!-- Card para Total de Documentos -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Documentos</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDocumentos }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-file fa-2x text-gray-300" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card para Total de Usuarios -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Usuarios</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsuarios }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-users fa-2x text-gray-300" aria-hidden="true"></i>
                                <!-- <i class="fas fa-users fa-2x text-gray-300"></i> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card para Total de Gerencias -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Gerencias</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalGerencias }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-building fa-2x text-gray-300" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card para Total de Subgerencias -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Total Subgerencias</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSubgerencias }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-sitemap fa-2x text-gray-300" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <canvas id="rolesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <canvas id="tiposChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <canvas id="fechasChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <canvas id="mesesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <canvas id="estadosChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <canvas id="gerenciasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Historial de documentos</h5>
                        <canvas id="historicoChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')

<script>
    // Datos para los gráficos
    const documentosPorGerencia = @json($documentosPorGerencia);
    const usuariosPorRol = @json($usuariosPorRol);
    const documentosPorTipo = @json($documentosPorTipo);
    const documentosPorFechaUltimoMes = @json($documentosPorFechaUltimoMes);
    const documentosPorFechaHistorico = @json($documentosPorFechaHistorico);
    const documentosPorMes = @json($documentosPorMes);
    const documentosPorEstado = @json($documentosPorEstado);

    // Función para generar colores aleatorios
    function generateRandomColors(numColors) {
        return Array.from({ length: numColors }, () =>
            `rgba(${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, 0.7)`
        );
    }

    // Gráfico de barras: Documentos por Gerencia
    // Mantener los datos originales fuera del gráfico
    const originalData = {
        labels: documentosPorGerencia.map(item => {
            // Si no hay gerencia, mostrar "Sin Gerencia"
            return item.gerencia ? item.gerencia.nombre : 'Sin Gerencia';
        }),
        data: documentosPorGerencia.map(item => item.total),
        backgroundColor: generateRandomColors(documentosPorGerencia.length)
    };

    // Gráfico de barras: Documentos por Gerencia
    const gerenciasChart = new Chart(document.getElementById('gerenciasChart'), {
        type: 'bar',
        data: {
            labels: originalData.labels,
            datasets: [{
                label: 'Documentos por Gerencia',
                data: originalData.data,
                backgroundColor: originalData.backgroundColor
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        generateLabels: function(chart) {
                            // Siempre usar los datos originales para las leyendas
                            return originalData.labels.map((label, i) => ({
                                text: label,
                                fillStyle: originalData.backgroundColor[i],
                                hidden: chart._hiddenData ? chart._hiddenData.has(i) : false,
                                index: i
                            }));
                        }
                    },
                    onClick: function(e, legendItem, legend) {
                        const chart = legend.chart;
                        const index = legendItem.index;

                        if (!chart._hiddenData) {
                            chart._hiddenData = new Set();
                        }

                        // Toggle el estado
                        if (chart._hiddenData.has(index)) {
                            chart._hiddenData.delete(index);
                        } else {
                            chart._hiddenData.add(index);
                        }

                        // Filtrar solo los datos visibles para las barras
                        const visibleData = originalData.data.filter((_, i) => !chart._hiddenData.has(i));
                        const visibleLabels = originalData.labels.filter((_, i) => !chart._hiddenData.has(i));
                        const visibleColors = originalData.backgroundColor.filter((_, i) => !chart._hiddenData.has(i));

                        // Actualizar solo las barras y etiquetas
                        chart.data.datasets[0].data = visibleData;
                        chart.data.labels = visibleLabels;
                        chart.data.datasets[0].backgroundColor = visibleColors;

                        chart.update();
                    }
                },
                title: {
                    display: true,
                    text: 'Documentos por Gerencia'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            if (Math.floor(value) === value) {
                                return value;
                            }
                        }
                    }
                },
                x: {
                    display: false  // Esto oculta todo el eje X
                }
            }
        }
    });

    // Gráfico de torta: Usuarios por Rol
    new Chart(document.getElementById('rolesChart'), {
        type: 'pie',
        data: {
            labels: usuariosPorRol.map(item => item.rol ? item.rol.nombre : 'Sin rol'),
            datasets: [{
                label: 'Número de Usuarios por Rol',
                data: usuariosPorRol.map(item => item.total),
                backgroundColor: generateRandomColors(usuariosPorRol.length)
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Usuarios por Rol'
                }
            }
        }
    });


    // Gráfico de torta: Documentos por Tipo
    console.log('Datos de documentosPorTipo:', documentosPorTipo);

    // Gráfico de torta: Documentos por Tipo
    new Chart(document.getElementById('tiposChart'), {
        type: 'pie',
        data: {
            labels: documentosPorTipo.map(item => item.tipo_documento ? item.tipo_documento.nombre : 'Sin tipo'),
            datasets: [{
                label: 'Número de Documentos por Tipo',
                data: documentosPorTipo.map(item => item.total),
                backgroundColor: generateRandomColors(documentosPorTipo.length)
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Documentos por Tipo'
                }
            }
        }
    });

    // Función auxiliar para formatear fechas
    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' });
    }

    // Gráfico del último mes
    const ultimoMesChart = new Chart(document.getElementById('fechasChart'), {
        type: 'line',
        data: {
            labels: documentosPorFechaUltimoMes.map(item => formatDate(item.fecha)),
            datasets: [{
                label: 'Documentos',
                data: documentosPorFechaUltimoMes.map(item => item.total),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1,
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            if (Math.floor(value) === value) {
                                return value;
                            }
                        }
                    }
                },
            },
            plugins: {
                title: {
                    display: true,
                    text: `Documentos del Último Mes (${getCurrentMonthName()})`
                }
            }
        }
    });



    // Crear los selects para el gráfico histórico
    // Preparar datos para los filtros
    const years = [...new Set(documentosPorFechaHistorico.map(item => item.anio))].sort();

    // Crear los selects para el gráfico histórico
    const filterContainer = document.createElement('div');
    filterContainer.className = 'd-flex gap-2 align-items-center mb-3';
    filterContainer.innerHTML = `
        <span>Filtrar por:</span>
        <select class="form-select" id="yearSelect" style="width: auto">
            ${years.map(year => `<option value="${year}">${year}</option>`).join('')}
        </select>
        <select class="form-select" id="monthSelect" style="width: auto">
            <option value="all">Todos los meses</option>
        </select>
    `;

    // Insertar los filtros antes del gráfico histórico
    const historicoChart = document.getElementById('historicoChart');
    historicoChart.parentElement.insertBefore(filterContainer, historicoChart);

    // Obtener referencias a los selects
    const yearSelect = document.getElementById('yearSelect');
    const monthSelect = document.getElementById('monthSelect');

    // Función para actualizar el select de meses
    function updateMonthSelect(year) {
        const months = documentosPorFechaHistorico
            .filter(item => item.anio == year)
            .map(item => item.mes)
            .filter((value, index, self) => self.indexOf(value) === index)
            .sort((a, b) => a - b);

        monthSelect.innerHTML = '<option value="all">Todos los meses</option>';
        months.forEach(month => {
            const monthName = new Date(2000, month - 1, 1).toLocaleDateString('es-ES', { month: 'long' });
            const option = new Option(monthName.charAt(0).toUpperCase() + monthName.slice(1), month);
            monthSelect.add(option);
        });
    }

    // Inicializar el gráfico histórico
    const historicoChartInstance = new Chart(historicoChart, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Documentos',
                data: [],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1,
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            if (Math.floor(value) === value) {
                                return value;
                            }
                        }
                    }
                },
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Histórico de Documentos'
                }
            }
        }
    });

    // Función para actualizar el gráfico histórico
    function updateHistoricoChart() {
        const selectedYear = yearSelect.value;
        const selectedMonth = monthSelect.value;

        let filteredData = documentosPorFechaHistorico.filter(item => item.anio == selectedYear);

        if (selectedMonth !== 'all') {
            filteredData = filteredData.filter(item => item.mes == selectedMonth);
        }

        // Ordenar por fecha
        filteredData.sort((a, b) => new Date(a.fecha) - new Date(b.fecha));

        historicoChartInstance.data.labels = filteredData.map(item => formatDate(item.fecha));
        historicoChartInstance.data.datasets[0].data = filteredData.map(item => item.total);

        const monthName = selectedMonth === 'all'
            ? 'todos los meses'
            : new Date(2000, monthSelect.value - 1, 1).toLocaleDateString('es-ES', { month: 'long' });

        historicoChartInstance.options.plugins.title.text =
            `Documentos de ${monthName} ${selectedYear}`;

        historicoChartInstance.update();
    }

    // Event listeners
    yearSelect.addEventListener('change', function() {
        updateMonthSelect(this.value);
        updateHistoricoChart();
    });

    monthSelect.addEventListener('change', updateHistoricoChart);

    // Inicialización
    if (years.length > 0) {
        updateMonthSelect(years[0]);
        updateHistoricoChart();
    }




    // Mostrar el total de documentos
    document.addEventListener('DOMContentLoaded', function() {
        const totalDocsElement = document.createElement('div');
        totalDocsElement.className = 'alert alert-info';
        totalDocsElement.innerHTML = `
            <strong>Total de Documentos:</strong> ${totalDocumentos}
        `;
        document.querySelector('.container-fluid').insertBefore(
            totalDocsElement,
            document.querySelector('.container-fluid').firstChild
        );
    });

    // Función para convertir número a nombre de mes
    function getMonthName(monthNumber) {
        const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        return monthNames[monthNumber - 1];
    }

    // Mantener los datos originales fuera del gráfico
    const originalMesesData = {
        labels: documentosPorMes.map(item => getMonthName(item.mes)),
        data: documentosPorMes.map(item => item.total),
        backgroundColor: generateRandomColors(documentosPorMes.length)
    };

    // Gráfico de barras: Documentos por Mes
    const mesesChart = new Chart(document.getElementById('mesesChart'), {
        type: 'bar',
        data: {
            labels: originalMesesData.labels,
            datasets: [{
                label: 'Documentos del ultimo Año',
                data: originalMesesData.data,
                backgroundColor: originalMesesData.backgroundColor
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        generateLabels: function(chart) {
                            // Siempre usar los datos originales para las leyendas
                            return originalMesesData.labels.map((label, i) => ({
                                text: label,
                                fillStyle: originalMesesData.backgroundColor[i],
                                hidden: chart._hiddenData ? chart._hiddenData.has(i) : false,
                                index: i
                            }));
                        }
                    },
                    onClick: function(e, legendItem, legend) {
                        const chart = legend.chart;
                        const index = legendItem.index;

                        if (!chart._hiddenData) {
                            chart._hiddenData = new Set();
                        }

                        // Toggle el estado
                        if (chart._hiddenData.has(index)) {
                            chart._hiddenData.delete(index);
                        } else {
                            chart._hiddenData.add(index);
                        }

                        // Filtrar solo los datos visibles para las barras
                        const visibleData = originalMesesData.data.filter((_, i) => !chart._hiddenData.has(i));
                        const visibleLabels = originalMesesData.labels.filter((_, i) => !chart._hiddenData.has(i));
                        const visibleColors = originalMesesData.backgroundColor.filter((_, i) => !chart._hiddenData.has(i));

                        // Actualizar solo las barras y etiquetas
                        chart.data.datasets[0].data = visibleData;
                        chart.data.labels = visibleLabels;
                        chart.data.datasets[0].backgroundColor = visibleColors;

                        chart.update();
                    }
                },
                title: {
                    display: true,
                    text: `Documentos del ultimo Año (${documentosPorMes[0]?.año || new Date().getFullYear()})`
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            if (Math.floor(value) === value) {
                                return value;
                            }
                        }
                    }
                },
                x: {
                    display: false
                }
            }
        }
    });


    // Mantener los datos originales fuera del gráfico
    const originalEstadosData = {
        labels: documentosPorEstado.map(item => item.estado),
        data: documentosPorEstado.map(item => item.total),
        backgroundColor: generateRandomColors(documentosPorEstado.length)
    };

    // Gráfico de barras: Documentos por Estado
    const estadosChart = new Chart(document.getElementById('estadosChart'), {
        type: 'bar',
        data: {
            labels: originalEstadosData.labels,
            datasets: [{
                label: 'Documentos por Estado',
                data: originalEstadosData.data,
                backgroundColor: originalEstadosData.backgroundColor
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        generateLabels: function(chart) {
                            // Siempre usar los datos originales para las leyendas
                            return originalEstadosData.labels.map((label, i) => ({
                                text: label,
                                fillStyle: originalEstadosData.backgroundColor[i],
                                hidden: chart._hiddenData ? chart._hiddenData.has(i) : false,
                                index: i
                            }));
                        }
                    },
                    onClick: function(e, legendItem, legend) {
                        const chart = legend.chart;
                        const index = legendItem.index;

                        if (!chart._hiddenData) {
                            chart._hiddenData = new Set();
                        }

                        // Toggle el estado
                        if (chart._hiddenData.has(index)) {
                            chart._hiddenData.delete(index);
                        } else {
                            chart._hiddenData.add(index);
                        }

                        // Filtrar solo los datos visibles para las barras
                        const visibleData = originalEstadosData.data.filter((_, i) => !chart._hiddenData.has(i));
                        const visibleLabels = originalEstadosData.labels.filter((_, i) => !chart._hiddenData.has(i));
                        const visibleColors = originalEstadosData.backgroundColor.filter((_, i) => !chart._hiddenData.has(i));

                        // Actualizar solo las barras y etiquetas
                        chart.data.datasets[0].data = visibleData;
                        chart.data.labels = visibleLabels;
                        chart.data.datasets[0].backgroundColor = visibleColors;

                        chart.update();
                    }
                },
                title: {
                    display: true,
                    text: 'Documentos por Estado'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            if (Math.floor(value) === value) {
                                return value;
                            }
                        }
                    }
                },
                x: {
                    display: false  // Esto oculta todo el eje X
                }
            }
        }
    });

    // OTROS DETALLES
    // OBTENER AÑO ACTUAL:
    const lastYear = documentosPorMes[0] ? documentosPorMes[0].año : new Date().getFullYear();
    document.getElementById('tituloCard').textContent = `Documentos por Mes (${lastYear})`;

    // Función para obtener el nombre del mes actual
    function getCurrentMonthName() {
        const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        const currentMonth = new Date().getMonth();
        return months[currentMonth];
    }
</script>
@endpush
