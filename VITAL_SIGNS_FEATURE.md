# Quick Vital Signs Feature Documentation

## Overview

Fitur Quick Vital Signs memungkinkan perawat untuk mencatat tanda-tanda vital pasien rawat inap dengan cepat melalui dashboard perawat. Fitur ini mencatat nama perawat yang melakukan pengukuran dan menyimpan data ke dalam sistem.

## Features

### 1. Form Quick Vital Signs
- **Input Fields:**
  - Blood Pressure (Sistolic/Diastolic)
  - Heart Rate (bpm)
  - Temperature (°C)
  - Respiratory Rate (/min)
  - Oxygen Saturation (%)
  - Consciousness Level (Alert/Drowsy/Confused/Unconscious)
  - Notes (optional)

- **Nurse Information:**
  - Automatically displays current logged-in nurse's name
  - Records nurse ID for audit trail
  - Shows nurse role (Owner/Doctor/Nurse/Administrator/Receptionist)

### 2. Data Validation
- **Authorization:** Only users with roles 1 (Owner), 3 (Nurse), or 4 (Admin) can record vital signs
- **Data Validation:**
  - Blood pressure range: Systolic (50-300), Diastolic (30-200)
  - Heart rate range: 30-200 bpm
  - Temperature range: 30-45°C
  - Respiratory rate range: 5-60/min
  - Oxygen saturation range: 70-100%
- **Security:** Nurses can only record under their own name (prevents impersonation)

### 3. Response & Feedback
- **Success Response:**
  - Patient name and room number
  - Formatted vital signs data
  - Nurse information with timestamp
  - Activity logging for audit trail

- **User Interface:**
  - Toast notification with success message
  - Detailed SweetAlert popup showing all recorded data
  - Auto-reset form after successful submission
  - Modal auto-close after recording

## Implementation Details

### Backend (Controller)
**File:** `app/Http/Controllers/RuanganController.php`

**Method:** `recordVitalSigns(Request $request)`

**Key Features:**
- Validates user authorization and data
- Fetches patient admission data
- Records nurse information from authenticated user
- Formats vital signs data for display
- Logs activity for audit trail
- Returns structured JSON response

**Helper Methods:**
- `getNurseRole($roleId)` - Maps role ID to role name
- `formatBloodPressure($systolic, $diastolic)` - Formats blood pressure display

### Frontend (Blade Template)
**File:** `resources/views/pages/ruangan/nurse-bed-dashboard.blade.php`

**Key Features:**
- Pre-fills nurse name from authenticated user
- CSRF protection
- Real-time form validation
- AJAX form submission
- Success/error handling with detailed messages
- Modal management

### API Endpoint
**URL:** `/kunjungan/nurse-dashboard/vital-signs`
**Method:** `POST`
**Authorization:** Required (Nurse roles: 1, 3, 4)

**Request Format:**
```json
{
    "admission_id": 1,
    "measurement_time": "2024-01-15T10:30:00",
    "recorded_by_id": 1,
    "blood_pressure_systolic": 120,
    "blood_pressure_diastolic": 80,
    "heart_rate": 72,
    "temperature": 36.5,
    "respiratory_rate": 18,
    "oxygen_saturation": 98,
    "consciousness_level": "alert",
    "notes": "Patient stable"
}
```

**Success Response:**
```json
{
    "success": true,
    "message": "Vital signs recorded successfully",
    "data": {
        "patient_name": "John Doe",
        "room_number": "101A",
        "blood_pressure": "120/80 mmHg",
        "heart_rate": "72 bpm",
        "temperature": "36.5°C",
        "respiratory_rate": "18/min",
        "oxygen_saturation": "98%",
        "recorded_at": "2024-01-15 10:30:00"
    },
    "nurse_info": {
        "name": "Jane Smith",
        "email": "jane@hospital.com",
        "role": "Nurse",
        "timestamp": "15/01/2024 10:30"
    }
}
```

## Usage Instructions

### For Nurses:
1. Access Nurse Dashboard (`/kunjungan/dashboard-bed-perawat`)
2. Find patient in the room grid
3. Click "Record Vital Signs" button
4. Fill in the vital signs form (your name will be pre-filled)
5. Submit the form
6. Review the confirmation dialog
7. The form will automatically reset for next recording

### For Administrators:
1. Monitor vital signs activity through audit logs
2. Review recorded data through patient records
3. Ensure proper authorization controls are in place

## Security Features

1. **Authentication Required:** All endpoints require authenticated users
2. **Role-Based Access:** Only specific roles can record vital signs
3. **Audit Trail:** All vital signs recordings are logged with user information
4. **Data Validation:** Comprehensive validation prevents invalid data entry
5. **Anti-Impersonation:** Users can only record under their own name

## Testing

### Manual Testing:
1. Use the test file `test_vital_signs.html` to test the API endpoint
2. Verify form validation works correctly
3. Check success/error messages display properly
4. Ensure audit logs are created

### Unit Testing Scenarios:
- Test with valid vital signs data
- Test with invalid data (out of range values)
- Test authorization (different user roles)
- Test anti-impersonation validation
- Test database integration
- Test audit logging

## Database Requirements

### Tables Used:
- `inpatient_admissions` - Patient admission data
- `users` - Nurse/user information
- `ruangans` - Room information
- `patients` - Patient basic information
- Activity log tables (for audit trail)

### Future Enhancements:
Consider creating a dedicated `vital_signs` table to store actual data instead of just logging:

```sql
CREATE TABLE vital_signs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admission_id BIGINT UNSIGNED NOT NULL,
    recorded_by_id BIGINT UNSIGNED NOT NULL,
    measurement_time TIMESTAMP NOT NULL,
    blood_pressure_systolic INT NULL,
    blood_pressure_diastolic INT NULL,
    heart_rate INT NULL,
    temperature DECIMAL(4,1) NULL,
    respiratory_rate INT NULL,
    oxygen_saturation INT NULL,
    consciousness_level ENUM('alert','drowsy','confused','unconscious') NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (admission_id) REFERENCES inpatient_admissions(id),
    FOREIGN KEY (recorded_by_id) REFERENCES users(id),
    
    INDEX idx_admission_date (admission_id, measurement_time),
    INDEX idx_recorded_by (recorded_by_id),
    INDEX idx_measurement_time (measurement_time)
);
```

## Support

For issues or questions about the Quick Vital Signs feature:
1. Check the browser console for JavaScript errors
2. Verify user has proper role permissions
3. Ensure all required fields are filled correctly
4. Check server logs for API errors
5. Verify database connections and patient admission data exists

## Changelog

### Version 1.0 (Current)
- Initial implementation of Quick Vital Signs feature
- Full nurse information integration
- Comprehensive data validation
- Audit trail logging
- User-friendly interface with detailed feedback
- Security controls and authorization checks