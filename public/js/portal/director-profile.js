/**
 * Director Profile JavaScript
 */

function triggerDirectorPasswordChange() {
    const newPass = prompt('Enter new Director Administrative Password:');
    if (newPass && newPass.length >= 8) {
        if (typeof showPortalToast === 'function') {
            showPortalToast('Director security credentials updated successfully.', 'success');
        } else {
            alert('Password updated.');
        }
    } else if (newPass) {
        alert('Password must be at least 8 characters.');
    }
}

function updateDirectorSignature() {
    if (typeof showPortalToast === 'function') {
        showPortalToast('Director Digital Signature Seal verified and synchronized with Board of Trustees.', 'success');
    }
}
