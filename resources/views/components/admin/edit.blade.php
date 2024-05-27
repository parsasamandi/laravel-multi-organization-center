<div class="container-fluid mt-3 right-text">
    {{-- List --}}
    <ol class="breadcrumb mb-4 right-text">
      <h5>ویرایش گزارش یک ردیف هزینه‌</h5>
    </ol>

    <form id="reportEditForm" class="form-horizontal details-form" enctype="multipart/form-data">
        {{ csrf_field() }}

        <span id="form_output"></span>

        <div class="row">
            <div class="col-md-12 mb-3">
                <!-- Date -->
                <label for="date">تاریخ:</label>
                <select id="general_info_id" name="general_info_id">
                    @foreach ($dates as $date)
                        <option value="{{ $date->id }}">
                            {{ $date->jalaliMonth }} {{ $date->jalaliYear }}
                        </option>
                    @endforeach
                </select>
            </div>


            <!-- Expenses -->
            <x-input type="number" key="expenses" placeholder="مبلغ هزینه"
                class="col-md-4 mb-3" value="{{ $report['expenses'] }}" />

            <!-- Range -->
            <x-input type="number" key="range" placeholder="ردیف هزینه"
                class="col-md-4 mb-3" value="{{ $report['range'] }}" />

            <!-- Type -->
            <div class="col-md-4">
                @include('includes.report.type')
            </div>

            <!-- Description -->
            <x-textarea key="description" placeholder="توضیحات" class="col-md-12 mb-3" value="{{ $report['description'] }}" />

        </div>

        <!-- File -->
        <h6>پیوست فایل صورتحساب بانکی</h6>  
        <input type="file" id="file" name="receipt" class="mb-3"
            accept=".pdf,.doc,.docx,.csv,application/msword,application/
            vnd.openxmlformats-officedocument.wordprocessingml.document,application/
            vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>


        <br/>

        <!-- Buttons -->
        <div class="form-group" align="center">
            <input type="submit" id="action" value="ویرایش" class="btn btn-primary" />
            <button type="button" id="return_button" class="btn btn-secondary" data-dismiss="modal">بازگشت</button>
            <input type="hidden" name="id" id="id" value="{{ $report['id'] }}"  />
        </div>

    </form>

</div>