<script>
    lucide.createIcons();
</script>



<script>
    function showLoading() {
        document.getElementById('loading-box').classList.add('active');
    }

    function hideLoading() {
        document.getElementById('loading-box').classList.remove('active');
    }

    window.onload = hideLoading;
    window.addEventListener("pageshow", hideLoading);

    document.addEventListener("DOMContentLoaded", function() {

        document.querySelectorAll("a").forEach(link => {
            link.addEventListener("click", function() {
                showLoading();
            });
        });

        document.querySelectorAll("form").forEach(form => {
            form.addEventListener("submit", function() {
                showLoading();
            });
        });

    });
</script>


<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

<script>
    const notyf = new Notyf({
        duration: 2500,
        position: {
            x: 'left',
            y: 'bottom'
        },
        types: [{
                type: 'success',
                background: '#fff',
                icon: false
            },
            {
                type: 'error',
                background: '#fff',
                icon: false
            }
        ]
    });
</script>
<script>
    <?php if(session('success')): ?>
    notyf.success("<?php echo session('success'); ?>");
    <?php endif; ?>

    <?php if(session('error')): ?>
    notyf.error("<?php echo session('error'); ?>");
    <?php endif; ?>
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.querySelectorAll('.group-box').forEach(function(group) {

        let checkboxes = group.querySelectorAll('.infaq-check');
        let title = group.querySelector('.group-title');

        function updateState() {
            let checked = group.querySelectorAll('.infaq-check:checked').length;

            if (checked > 0) {
                group.classList.add('border-primary');
                title.classList.remove('bg-light', 'text-dark');
                title.classList.add('bg-primary', 'text-white');
            } else {
                group.classList.remove('border-primary');
                title.classList.remove('bg-primary', 'text-white');
                title.classList.add('bg-light', 'text-dark');
            }
        }

        checkboxes.forEach(function(cb) {
            cb.addEventListener('change', updateState);
        });

    });
</script>

<script>
    document.querySelectorAll('.infaq-check').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {

            let kelas = this.dataset.kelas;

            let semua = document.querySelectorAll('.infaq-check[data-kelas="' + kelas + '"]');
            let adaYangDicek = false;

            semua.forEach(function(cb) {
                if (cb.checked) adaYangDicek = true;
            });

            let kelasCheckbox = document.querySelector('.kelas-check[value="' + kelas + '"]');
            let box = kelasCheckbox.closest('.group-box');
            let title = box.querySelector('.group-title');

            if (adaYangDicek) {
                kelasCheckbox.checked = true;

                box.classList.add('border-primary');
                title.classList.remove('bg-light', 'text-dark');
                title.classList.add('bg-primary', 'text-white');

            } else {
                kelasCheckbox.checked = false;

                box.classList.remove('border-primary');
                title.classList.remove('bg-primary', 'text-white');
                title.classList.add('bg-light', 'text-dark');
            }

        });
    });
</script>
<script>
    function clearInput() {
        document.getElementById('searchInput').value = '';
    }
</script>

<script>
    $(document).ready(function() {

        $('#searchInput').on('keyup', function() {

            let q = $(this).val();

            if (q.length < 1) {
                $('#dropdownResult').html('');
                return;
            }

            $.get('/search-siswa', {
                q: q
            }, function(data) {

                let html = '';

                data.forEach(function(item) {
                    html += `
                    <div class="item-siswa"
                         data-id="${item.id_siswa}"
                         data-nama="${item.nama_siswa}"
                         data-kelas="${item.nama_kelas}"
                         data-angkatan="${item.id_angkatan}"
                         style="padding:8px; border-bottom:1px solid #ddd; cursor:pointer;">
                        ${item.nama_siswa} - ${item.nama_kelas}
                    </div>
                `;
                });

                $('#dropdownResult').html(html);
            });

        });


        $(document).on('click', '.item-siswa', function() {

            let id = $(this).data('id');
            let nama = $(this).data('nama');
            let kelas = $(this).data('kelas');
            let angkatan = $(this).data('angkatan');

            console.log("SELECT:", id, nama, kelas);

            // isi form
            $('#searchInput').val(nama);
            $('#namaField').val(nama);
            $('#kelasField').val(kelas);
            $('#idSiswa').val(id);
            $('#idAngkatan').val(angkatan);

            $('#dropdownResult').html('');


            $.ajax({
                url: '/get-infaq/' + id,
                type: 'GET',
                success: function(data) {

                    console.log("INFAQ RESULT:", data);

                    let html = '';

                    if (!data || data.length === 0) {

                        html = `
        <tr>
            <td colspan="2">Tidak ada Data Infaq</td>
            
        </tr>
    `;

                    } else {

                        data.forEach(function(item) {

                            html += `
            <tr>
    <td>
        ${item.name} <br>
        Rp ${Number(item.harga).toLocaleString()}
    </td>

    <td>
        <input type="number"
       class="form-control bayar-infaq"
       data-id="${item.id}"
       data-name="${item.name}"
       data-max="${item.harga}"
       name="bayar_infaq[${item.id}]"
       min="0"
       max="${item.harga}"
       placeholder="Nominal bayar">
    </td>
</tr>
        `;
                        });

                    }

                    $('#infaqList').html(html);
                },
                error: function(xhr) {
                    console.log("ERROR INFAQ:", xhr.responseText);
                }
            });

        });

    });
</script>

<script>
    $(document).on('input', '.bayar-infaq', function() {

        let html = '';
        let total = 0;

        $('.bayar-infaq').each(function() {

            let nominal = parseInt($(this).val()) || 0;

            if (nominal > 0) {

                let name = $(this).data('name');

                total += nominal;

                html += `
                <div class="d-flex justify-content-between">
                    <span>${name}</span>
                    <span>${nominal.toLocaleString()}</span>
                </div>
            `;
            }
        });

        $('#summaryList').html(html);
        $('#totalBayar').text(total.toLocaleString());

        let max = parseInt($(this).data('max'));
        let val = parseInt($(this).val()) || 0;


        if (val < 0) {
            $(this).val(0);
        }


        if (val > max) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }

    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tooltipTriggerList = document.querySelectorAll('[title]');
        tooltipTriggerList.forEach(function(el) {
            new bootstrap.Tooltip(el);
        });
    });
</script>

<script>
    function handleSubmit(form) {
        const btn = form.querySelector('#btnSimpan');
        btn.disabled = true;
        btn.innerText = 'Menyimpan...';
    }
</script>
<script>
    function notsetStatusIcon(iconName) {
        const oldIcon = document.getElementById('wa-status-icon');

        const newIcon = document.createElement('i');
        newIcon.setAttribute('data-lucide', iconName);
        newIcon.setAttribute('id', 'wa-status-icon');
        newIcon.style.width = '12px';
        newIcon.style.height = '12px';

        if (iconName === 'loader-circle') {
            newIcon.classList.add('spin-icon');
        }

        oldIcon.replaceWith(newIcon);
        lucide.createIcons();
    }

    function notcekWaGateway() {
        const statusElement = document.getElementById('wa-gateway-status');
        const textElement = document.getElementById('wa-status-text');

        textElement.innerText = '';
        statusElement.className = 'badge rounded-pill text-bg-secondary d-flex align-items-center';
        setStatusIcon('loader-circle');

        fetch('/cek-fonnte')
            .then(response => response.json())
            .then(data => {
                if (data.connected) {
                    textElement.innerText = '';
                    statusElement.className = 'badge rounded-pill text-bg-success d-flex align-items-center';
                    setStatusIcon('check');
                } else {
                    textElement.innerText = '';
                    statusElement.className = 'badge rounded-pill text-bg-danger d-flex align-items-center';
                    setStatusIcon('x');
                }
            })
            .catch(() => {
                textElement.innerText = '';
                statusElement.className = 'badge rounded-pill text-bg-danger d-flex align-items-center gap-1';
                setStatusIcon('wifi-off');
            });
    }

    cekWaGateway();
    setInterval(cekWaGateway, 5000); 
</script>



</body>

</html>
