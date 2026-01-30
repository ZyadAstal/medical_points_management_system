<!-- Modals for Manager Staff Management -->
<div aria-hidden="true" class="modal-overlay" id="userAddOverlay">
    <div aria-labelledby="userAddTitle" aria-modal="true" class="user-add-modal" role="dialog">
        <section aria-label="Add User Form" class="user-add-panel">
            <h2 class="user-add-title" id="userAddTitle">اضافة موظف جديد</h2>
            <div aria-hidden="true" class="user-add-divider"></div>
            <form action="{{ route('manager.staff.store') }}" method="POST" autocomplete="off" class="user-add-fields" id="userAddForm">
                @csrf
                <div class="user-add-field">
                    <label class="user-add-label" for="ua_name">الاسم</label>
                    <input class="user-add-input" id="ua_name" name="name" placeholder="أدخل الاسم كامل" type="text" required/>
                </div>
                <div class="user-add-field">
                    <label class="user-add-label" for="ua_username">اسم المستخدم</label>
                    <input class="user-add-input" id="ua_username" name="username" placeholder="أدخل اسم المستخدم" type="text" required/>
                </div>
                <div class="user-add-field">
                    <label class="user-add-label" for="ua_email">البريد الالكتروني</label>
                    <input class="user-add-input" id="ua_email" name="email" placeholder="أدخل البريد الالكتروني" type="email" required/>
                </div>
                <div class="user-add-field">
                    <label class="user-add-label" for="ua_role">الدور</label>
                    <select class="user-add-input user-add-select" id="ua_role" name="role_id" required>
                        <option disabled="" selected="" value="">اختر الدور</option>
                        @foreach(App\Models\Role::whereIn('name', ['Doctor', 'Pharmacist', 'Reception'])->get() as $role)
                            @php
                                $roleAr = [
                                    'Doctor' => 'طبيب',
                                    'Pharmacist' => 'صيدلي',
                                    'Reception' => 'استقبال'
                                ][$role->name] ?? $role->name;
                            @endphp
                            <option value="{{ $role->id }}">{{ $roleAr }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="user-add-field">
                    <label class="user-add-label" for="ua_password">كلمة المرور</label>
                    <input class="user-add-input" id="ua_password" name="password" placeholder="أنشئ كلمة المرور" type="password" required/>
                </div>
                <div class="user-add-field">
                    <label class="user-add-label" for="ua_password_confirm">تأكيد كلمة المرور</label>
                    <input class="user-add-input" id="ua_password_confirm" name="password_confirmation" placeholder="تأكيد كلمة المرور" type="password" required/>
                </div>
                <div class="user-add-actions">
                    <button class="user-add-btn save btn-drug-primary" type="submit">حفظ</button>
                    <button class="user-add-btn cancel btn-drug-cancel" id="userAddCancelBtn" type="button">إلغاء</button>
                </div>
            </form>
        </section>
    </div>
</div>

<div aria-hidden="true" class="modal-overlay" id="userEditOverlay">
    <div aria-labelledby="userEditTitle" aria-modal="true" class="user-edit-modal" role="dialog">
        <section aria-label="Edit User Form" class="user-edit-panel">
            <h2 class="user-edit-title" id="userEditTitle">تعديل موظف</h2>
            <div aria-hidden="true" class="user-edit-divider"></div>
            <form id="userEditForm" method="POST">
                @csrf
                @method('PUT')
                <div class="user-edit-fields">
                    <div class="user-edit-field">
                        <label class="user-edit-label" for="edit-name">الاسم</label>
                        <input class="user-edit-input" id="edit-name" name="name" type="text" required/>
                    </div>
                    <div class="user-edit-field">
                        <label class="user-edit-label" for="edit-username">اسم المستخدم</label>
                        <input class="user-edit-input" id="edit-username" name="username" type="text" required/>
                    </div>
                    <div class="user-edit-field">
                        <label class="user-edit-label" for="edit-email">البريد الالكتروني</label>
                        <input class="user-edit-input" id="edit-email" name="email" type="email" required/>
                    </div>
                    <div class="user-edit-field">
                        <label class="user-edit-label" for="edit-role">الدور</label>
                        <select class="user-edit-select" id="edit-role" name="role_id" required>
                            @foreach(App\Models\Role::whereIn('name', ['Doctor', 'Pharmacist', 'Reception'])->get() as $role)
                                @php
                                    $roleAr = [
                                        'Doctor' => 'طبيب',
                                        'Pharmacist' => 'صيدلي',
                                        'Reception' => 'استقبال'
                                    ][$role->name] ?? $role->name;
                                @endphp
                                <option value="{{ $role->id }}">{{ $roleAr }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="user-edit-field">
                        <label class="user-edit-label" for="edit-password">كلمة المرور (اختياري)</label>
                        <input class="user-edit-input" id="edit-password" name="password" placeholder="اتركه فارغا للإبقاء على الحالية" type="password"/>
                    </div>
                    <div class="user-edit-actions">
                        <button class="user-edit-btn update btn-drug-primary" type="submit">تحديث</button>
                        <button class="user-edit-btn cancel btn-drug-cancel" id="userEditCancelBtn" type="button">إلغاء</button>
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>
