<div aria-hidden="true" class="modal-overlay" id="universalDeleteOverlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999;">
    <div aria-modal="true" role="dialog" style="background: white; border-radius: 10px; width: 400px; max-width: 90%; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); position: relative; animation: slideDown 0.3s ease;">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: #fee2e2; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18"></path>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </div>
            <h2 style="font-size: 20px; font-weight: bold; color: #111827; margin: 0 0 10px; font-family: 'Inter', sans-serif;">تأكيد الحذف</h2>
            <p style="font-size: 14px; color: #6b7280; margin: 0; font-family: 'Inter', sans-serif;">هل أنت متأكد من أنك تريد حذف هذا العنصر؟ لا يمكن التراجع عن هذا الإجراء.</p>
        </div>
        
        <form id="universalDeleteForm" method="POST" style="margin: 0;">
            @csrf
            @method('DELETE')
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button type="button" onclick="closeUniversalDeleteModal()" style="padding: 10px 20px; border-radius: 8px; border: 1px solid #d1d5db; background: white; color: #374151; font-weight: 500; cursor: pointer; flex: 1; font-family: 'Inter', sans-serif; font-size: 15px; transition: background 0.2s;">إلغاء</button>
                <button type="submit" style="padding: 10px 20px; border-radius: 8px; border: none; background: #ef4444; color: white; font-weight: 500; cursor: pointer; flex: 1; font-family: 'Inter', sans-serif; font-size: 15px; transition: background 0.2s;" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">نعم، احذف</button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes slideDown {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

<script>
    function openUniversalDeleteModal(actionUrl) {
        const overlay = document.getElementById('universalDeleteOverlay');
        const form = document.getElementById('universalDeleteForm');
        form.action = actionUrl;
        overlay.style.display = 'flex';
    }

    function closeUniversalDeleteModal() {
        const overlay = document.getElementById('universalDeleteOverlay');
        overlay.style.display = 'none';
        document.getElementById('universalDeleteForm').action = '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('form').forEach(form => {
            const methodInput = form.querySelector('input[name="_method"][value="DELETE"], input[name="_method"][value="delete"]');
            
            if (methodInput && form.id !== 'universalDeleteForm') {
                if (form.hasAttribute('onsubmit') && form.getAttribute('onsubmit').includes('confirm')) {
                    form.removeAttribute('onsubmit');
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        openUniversalDeleteModal(this.action);
                    });
                }
            }
        });
    });
</script>
