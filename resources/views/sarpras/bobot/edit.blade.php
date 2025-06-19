<!-- Isi yang dimuat oleh AJAX ke dalam #myModal -->
<div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 relative border-t border-blue-700">

    <button id="modal-close" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 cursor-pointer">
        <i class="fas fa-times"></i>
    </button>

    <h2 class="text-xl font-semibold mb-2 text-center">Edit Data Bobot Kriteria</h2>
    <div class="w-[220px] h-1 bg-yellow-400 mx-auto mt-1 mb-6 rounded"></div>

    <form id="form-edit-bobot" action="{{ route('sarpras.bobot.update') }}" method="POST" class="grid grid-cols-1 gap-4">
        @csrf
        @method('PUT')

        @foreach ($kriteria as $k)
        <div>
            <label class="block text-sm font-medium mb-1">{{ $k->nama_kriteria }}<span class="text-red-500"> *</span></label>
            <input type="number" name="{{ 'bobot_' . $k->id_kriteria }}" id="{{ 'bobot_' . $k->id_kriteria }}" class="w-full border rounded-md px-3 py-2 text-sm bobot-input" value="{{ $k->bobot }}" placeholder="Bobot Kriteria (%)" required min="0" max="100" step="1">
            <span id="{{ 'bobot_' . $k->id_kriteria }}-error" class="text-xs text-red-500 mt-1 error-text"></span>
        </div>
        @endforeach

        <div class="flex justify-between items-center mt-4 mb-2">
            <div class="text-base font-medium">
                Total Bobot: <span id="total-bobot" class="font-bold">0%</span>
            </div>
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md cursor-pointer">
                <div class="flex justify-center items-center gap-[10px]">
                    <img src="{{ asset('icons/light/Check-circle.svg') }}" alt="Simpan" class="w-6 h-6">
                    <p>Simpan</p>
                </div>
            </button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>
<script>
$(document).ready(function() {
    // Fungsi untuk menghitung dan menampilkan total bobot
    function updateTotalBobot() {
        let total = 0;
        $('.bobot-input').each(function() {
            let val = parseInt($(this).val());
            total += isNaN(val) ? 0 : val;
        });
        $('#total-bobot').text(total + '%');
        // Ubah warna teks berdasarkan total
        if (total === 100) {
            $('#total-bobot').removeClass('text-red-500').addClass('text-green-500');
        } else {
            $('#total-bobot').removeClass('text-green-500').addClass('text-red-500');
        }
    }

    // Panggil fungsi saat halaman dimuat
    updateTotalBobot();

    // Perbarui total saat input berubah
    $('.bobot-input').on('input', updateTotalBobot);

    // Tambahkan metode custom untuk validasi total bobot
    $.validator.addMethod("totalBobotSeratus", function(value, element) {
        let total = 0;
        for (let i = 1; i <= 6; i++) {
            let val = parseInt($(`#bobot_${i}`).val());
            total += isNaN(val) ? 0 : val;
        }
        return total === 100;
    }, "Jumlah total bobot harus sama dengan 100%");

    let rules = {};
    $.each([1, 2, 3, 4, 5, 6], function(i, num) {
        rules['bobot_' + num] = {
            required: true,
            digits: true,
            min: 0,
            max: 100
        };
    });

    let messages = {};
    $.each([1, 2, 3, 4, 5, 6], function(i, num) {
        messages['bobot_' + num] = {
            required: "Bobot " + num + " harus diisi",
            digits: "Bobot harus berupa angka bulat",
            min: "Minimal bernilai 0",
            max: "Maksimal bernilai 100",
        };
    });

    rules['bobot_6'].totalBobotSeratus = true;

    $("#form-edit-bobot").validate({
        errorElement: 'span',
        errorClass: 'text-xs text-red-500 mt-1 error-text',
        highlight: function(element) {},
        unhighlight: function(element) {},
        errorPlacement: function(error, element) {
            var errorContainer = element.next('.error-text');
            if (errorContainer.length) {
                errorContainer.replaceWith(error);
            } else {
                error.insertAfter(element);
            }
        },
        rules: rules,
        messages: messages,
    });
});
</script>