@extends('layouts.ong')

@section('titulo', 'Painel da Instituição')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/ong/pontos.css') }}?v=3.0">
    <link rel="stylesheet" href="{{ asset('css/ong/dashboard.css') }}?v=3.1">
@endsection

@section('conteudo')
<div class="ong-dashboard-container">

    {{-- BANNER DE BOAS-VINDAS --}}
    <div class="dashboard-header-card">
        <div class="dashboard-header-content">
            <div>
                <div class="dashboard-banner-brand">
                    <img src="{{ asset('img/logo-ecopoints.png') }}" alt="EcoPoints - Painel da Instituição" class="dashboard-banner-logo">
                </div>
                <h1 class="dashboard-title">Painel da Instituição</h1>
                <p class="dashboard-welcome">
                    Seja bem-vindo(a), <strong>{{ $ong->nome ?? auth()->user()->name }}</strong> ({{ auth()->user()->email }})
                </p>
            </div>
            <div>
                <a href="{{ route('ong.pontos.create') }}" class="dashboard-btn-create">
                    + Cadastrar Novo Ponto
                </a>
            </div>
        </div>
    </div>

    {{-- MÉTRICAS --}}
    <div class="section-title">
        <span>Métricas da Sua Organização</span>
    </div>

    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-card-top">
                <span class="metric-card-title">Pontos de Coleta</span>
            </div>
            <div class="metric-value">{{ $totalPontos }}</div>
            <div class="metric-card-footer">Total de locais cadastrados</div>
        </div>

        <div class="metric-card">
            <div class="metric-card-top">
                <span class="metric-card-title">Pontos Ativos</span>
            </div>
            <div class="metric-value">{{ $pontosAtivos }}</div>
            <div class="metric-card-footer">Locais em operação no mapa</div>
        </div>

        <div class="metric-card">
            <div class="metric-card-top">
                <span class="metric-card-title">Pontos Inativos</span>
            </div>
            <div class="metric-value">{{ $pontosInativos }}</div>
            <div class="metric-card-footer">Temporariamente pausados</div>
        </div>

        <div class="metric-card">
            <div class="metric-card-top">
                <span class="metric-card-title">Cidades Atendidas</span>
            </div>
            <div class="metric-value">{{ $totalCidades }}</div>
            <div class="metric-card-footer">Municípios com cobertura</div>
        </div>

        <div class="metric-card">
            <div class="metric-card-top">
                <span class="metric-card-title">Categorias de Materiais</span>
            </div>
            <div class="metric-value">{{ $totalMateriais }}</div>
            <div class="metric-card-footer">Tipos de acervo arrecadados</div>
        </div>

        <div class="metric-card">
            <div class="metric-card-top">
                <span class="metric-card-title">Solicitações de Alteração</span>
                @if($solicitacoesPendentes > 0)
                    <span class="metric-pending-tag">{{ $solicitacoesPendentes }} pendente(s)</span>
                @endif
            </div>
            <div class="metric-value">{{ $totalSolicitacoes }}</div>
            <div class="metric-card-footer">Pedidos enviados ao administrador</div>
        </div>
    </div>

    {{-- GRÁFICOS --}}
    <div class="section-title">
        <span>Desempenho e Distribuição Operacional</span>
    </div>

    <div class="charts-grid-top">
        <div class="chart-card">
            <div class="chart-card-header">
                <div>
                    <h3 class="chart-card-title">Status Operacional dos Pontos</h3>
                    <p class="chart-card-subtitle">Proporção entre pontos ativos e inativos</p>
                </div>
            </div>
            <div class="chart-wrapper">
                @if($totalPontos > 0)
                    <canvas id="chartStatusPontos"></canvas>
                @else
                    <div class="chart-empty-state">
                        <p>Nenhum ponto cadastrado para exibir status.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-card-header">
                <div>
                    <h3 class="chart-card-title">Pontos de Coleta por Cidade</h3>
                    <p class="chart-card-subtitle">Distribuição geográfica dos postos de arrecadação da ONG</p>
                </div>
            </div>
            <div class="chart-wrapper">
                @if(!empty($graficoCidades))
                    <canvas id="chartCidadesPontos"></canvas>
                @else
                    <div class="chart-empty-state">
                        <p>Cadastre pontos de coleta para visualizar a distribuição por município.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="charts-grid-bottom">
        <div class="chart-card">
            <div class="chart-card-header">
                <div>
                    <h3 class="chart-card-title">Materiais Aceitos nos Pontos</h3>
                    <p class="chart-card-subtitle">Tipos de acervo recebidos nos seus locais</p>
                </div>
            </div>
            <div class="chart-wrapper chart-wrapper-tall">
                @if(!empty($graficoMateriais) && collect($graficoMateriais)->sum('total') > 0)
                    <canvas id="chartMateriais"></canvas>
                @else
                    <div class="chart-empty-state">
                        <p>Vincule materiais aceitos aos seus pontos para gerar esta estatística.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-card-header">
                <div>
                    <h3 class="chart-card-title">Solicitações Cadastrais</h3>
                    <p class="chart-card-subtitle">Pendentes, Aprovadas e Recusadas</p>
                </div>
            </div>
            <div class="chart-wrapper chart-wrapper-tall">
                @if($totalSolicitacoes > 0)
                    <canvas id="chartSolicitacoes"></canvas>
                @else
                    <div class="chart-empty-state">
                        <p>Você ainda não enviou solicitações de alteração de nome ou endereço.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-card-header">
                <div>
                    <h3 class="chart-card-title">Evolução de Novos Pontos</h3>
                    <p class="chart-card-subtitle">Histórico de expansão de locais por mês</p>
                </div>
            </div>
            <div class="chart-wrapper chart-wrapper-tall">
                @if(!empty($graficoPontosMes))
                    <canvas id="chartPontosMes"></canvas>
                @else
                    <div class="chart-empty-state">
                        <p>Histórico mensal disponível após o cadastro de pontos.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- PONTOS RECENTES --}}
    <div class="recent-pontos-card">
        <div class="recent-pontos-header">
            <h2 class="recent-pontos-title">Pontos de Coleta Recentes</h2>
            @if ($totalPontos > 0)
                <a href="{{ route('ong.pontos.index') }}" class="recent-pontos-link">
                    Gerenciar todos os pontos ({{ $totalPontos }}) &rarr;
                </a>
            @endif
        </div>

        @if ($ultimosPontos->isNotEmpty())
            <table class="recent-table">
                <thead>
                    <tr>
                        <th>Nome do Ponto</th>
                        <th>Endereço / Cidade</th>
                        <th>Materiais Aceitos</th>
                        <th>Status</th>
                        <th style="text-align: right;">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ultimosPontos as $ponto)
                        <tr>
                            <td>
                                <strong style="color: #3B2A1F; font-size: 0.95rem;">{{ $ponto->nome }}</strong>
                                @if ($ponto->horario_funcionamento)
                                    <div style="font-size: 0.8rem; color: #8C6549; margin-top: 2px;">
                                        {{ $ponto->horario_funcionamento }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="color: #3B2A1F; font-weight: 500;">
                                    {{ $ponto->endereco }}{{ $ponto->numero ? ', ' . $ponto->numero : '' }}
                                </div>
                                <small style="color: #6F4E37;">{{ $ponto->cidade }}/{{ $ponto->estado }}</small>
                            </td>
                            <td>
                                <div class="materiais-badges">
                                    @forelse ($ponto->materiaisAceitos as $material)
                                        <span class="badge-material">{{ $material->nome }}</span>
                                    @empty
                                        <span style="color: #8C6549; font-size: 0.8rem; font-style: italic;">Nenhum material selecionado</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                @if ($ponto->ativo)
                                    <span class="status-pill status-active">Ativo</span>
                                @else
                                    <span class="status-pill status-inactive">Inativo</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <button
                                    type="button"
                                    class="recent-btn-action"
                                    onclick="abrirModalEditarPonto(this)"
                                    data-action="{{ route('ong.pontos.update', $ponto) }}"
                                    data-nome="{{ $ponto->nome }}"
                                    data-cep="{{ $ponto->cep }}"
                                    data-telefone="{{ $ponto->telefone_contato }}"
                                    data-endereco="{{ $ponto->endereco }}"
                                    data-numero="{{ $ponto->numero }}"
                                    data-complemento="{{ $ponto->complemento }}"
                                    data-bairro="{{ $ponto->bairro }}"
                                    data-cidade="{{ $ponto->cidade }}"
                                    data-estado="{{ $ponto->estado }}"
                                    data-horario="{{ $ponto->horario_funcionamento }}"
                                    data-descricao="{{ $ponto->descricao }}"
                                    data-ativo="{{ $ponto->ativo ? '1' : '0' }}"
                                    data-materiais-ids="{{ $ponto->materiaisAceitos->pluck('id')->implode(',') }}"
                                >
                                    Editar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding: 2.5rem 1.5rem; text-align: center; color: #6F4E37;">
                <p style="margin: 0 0 0.75rem 0;">Sua organização ainda não possui nenhum ponto de coleta cadastrado.</p>
                <a href="{{ route('ong.pontos.create') }}" class="dashboard-btn-create" style="box-shadow: none; display: inline-flex;">
                    + Cadastrar Ponto Agora
                </a>
            </div>
        @endif
    </div>

</div>

@include('ong.pontos._modal_editar')

@push('scripts')
{{-- SCRIPTS DE GRÁFICOS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const corCoffeeDark = '#3B2A1F';
    const corCoffeeRich = '#6F4E37';
    const corCaramel    = '#B08968';
    const corSand       = '#D4B996';
    const corCream      = '#EDE3D4';
    const corIvory      = '#FFF9F0';

    // Gráfico: Status dos Pontos
    @if($totalPontos > 0)
    const ctxStatus = document.getElementById('chartStatusPontos');
    if (ctxStatus) {
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Pontos Ativos', 'Pontos Inativos'],
                datasets: [{
                    data: [{{ $graficoStatus['ativos'] }}, {{ $graficoStatus['inativos'] }}],
                    backgroundColor: [corCoffeeRich, corSand],
                    borderColor: '#FFFFFF',
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: corCoffeeDark,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: 600 },
                            padding: 14
                        }
                    }
                },
                cutout: '68%'
            }
        });
    }
    @endif

    // Gráfico: Pontos por Cidade
    @if(!empty($graficoCidades))
    const ctxCidades = document.getElementById('chartCidadesPontos');
    if (ctxCidades) {
        const cidadesData = @json($graficoCidades);
        new Chart(ctxCidades, {
            type: 'bar',
            data: {
                labels: cidadesData.map(c => c.cidade),
                datasets: [{
                    label: 'Pontos Cadastrados',
                    data: cidadesData.map(c => c.total),
                    backgroundColor: corCaramel,
                    hoverBackgroundColor: corCoffeeRich,
                    borderRadius: 8,
                    maxBarThickness: 46
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            color: corCoffeeRich,
                            font: { family: "'Plus Jakarta Sans', sans-serif" }
                        },
                        grid: { color: 'rgba(111, 78, 55, 0.08)' }
                    },
                    x: {
                        ticks: {
                            color: corCoffeeDark,
                            font: { family: "'Plus Jakarta Sans', sans-serif", weight: 600 }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }
    @endif

    // Gráfico: Materiais Aceitos
    @if(!empty($graficoMateriais) && collect($graficoMateriais)->sum('total') > 0)
    const ctxMateriais = document.getElementById('chartMateriais');
    if (ctxMateriais) {
        const materiaisData = @json($graficoMateriais);
        new Chart(ctxMateriais, {
            type: 'bar',
            data: {
                labels: materiaisData.map(m => {
                    let nome = m.nome;
                    if (nome.length > 22) nome = nome.substring(0, 20) + '...';
                    return nome;
                }),
                datasets: [{
                    label: 'Pontos Aceitando',
                    data: materiaisData.map(m => m.total),
                    backgroundColor: [corCoffeeDark, corCoffeeRich, corCaramel, corSand, '#8C6549'],
                    borderRadius: 6,
                    maxBarThickness: 24
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0, color: corCoffeeRich },
                        grid: { color: 'rgba(111, 78, 55, 0.08)' }
                    },
                    y: {
                        ticks: { color: corCoffeeDark, font: { weight: 600, size: 11 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }
    @endif

    // Gráfico: Solicitações
    @if($totalSolicitacoes > 0)
    const ctxSolicitacoes = document.getElementById('chartSolicitacoes');
    if (ctxSolicitacoes) {
        new Chart(ctxSolicitacoes, {
            type: 'doughnut',
            data: {
                labels: ['Pendentes', 'Aprovadas', 'Recusadas'],
                datasets: [{
                    data: [
                        {{ $graficoSolicitacoes['pendentes'] }},
                        {{ $graficoSolicitacoes['aprovadas'] }},
                        {{ $graficoSolicitacoes['recusadas'] }}
                    ],
                    backgroundColor: ['#F59E0B', '#2F6929', '#B91C1C'],
                    borderColor: '#FFFFFF',
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: corCoffeeDark,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: 600 },
                            padding: 10
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
    @endif

    // Gráfico: Evolução Mensal
    @if(!empty($graficoPontosMes))
    const ctxPontosMes = document.getElementById('chartPontosMes');
    if (ctxPontosMes) {
        const mesData = @json($graficoPontosMes);
        new Chart(ctxPontosMes, {
            type: 'line',
            data: {
                labels: mesData.map(m => m.mes),
                datasets: [{
                    label: 'Pontos Criados',
                    data: mesData.map(m => m.total),
                    borderColor: corCoffeeDark,
                    backgroundColor: 'rgba(212, 185, 150, 0.35)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 3,
                    pointBackgroundColor: corCoffeeRich,
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0, color: corCoffeeRich },
                        grid: { color: 'rgba(111, 78, 55, 0.08)' }
                    },
                    x: {
                        ticks: { color: corCoffeeDark, font: { weight: 600, size: 11 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }
    @endif
</script>
@endpush
@endsection
