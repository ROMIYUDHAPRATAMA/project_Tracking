document.addEventListener('DOMContentLoaded', () => {
  const data = window.DASHBOARD_DATA || {
    incompleteBreakdown: [3,0,0],
    taskCompletion: [0,3]
  };

  const barEl = document.getElementById('barIncomplete');
  if (barEl && window.Chart) {
    new Chart(barEl, {
      type: 'bar',
      data: {
        labels: ['To do', 'Doing', 'Done'],
        datasets: [{ label: 'Total', data: data.incompleteBreakdown, borderWidth: 1 }]
      },
      options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision:0 } } }
      }
    });
  }

  const doughnutEl = document.getElementById('doughnutTasks');
  if (doughnutEl && window.Chart) {
    new Chart(doughnutEl, {
      type: 'doughnut',
      data: {
        labels: ['Complete', 'Incomplete'],
        datasets: [{ data: data.taskCompletion, borderWidth: 0 }]
      },
      options: { plugins: { legend: { display: false } }, cutout: '65%' }
    });
  }

  const btn = document.getElementById('btnSidebarToggle');
  if (btn) btn.addEventListener('click', () =>
    document.querySelector('.sidebar').classList.toggle('is-open')
  );
});
