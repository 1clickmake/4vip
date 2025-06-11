
// 정렬 처리 (sort)
document.addEventListener('DOMContentLoaded', function() {
    const sortableHeaders = document.querySelectorAll('.sortable');
    
    // 요소 존재 여부 체크
    const sortFieldElement = document.getElementById('sort_field');
    const sortOrderElement = document.getElementById('sort_order');

    // 요소가 존재할 때만 값 설정
    const sort_field = sortFieldElement ? sortFieldElement.value : null;
    const sort_order = sortOrderElement ? sortOrderElement.value : null;

    sortableHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            if (sort_field === null || sort_order === null) {
                return; // 요소가 없으면 실행하지 않음
            }

            const field = this.dataset.field;
            let newOrder = 'ASC';
            if (field === sort_field && sort_order === 'ASC') {
                newOrder = 'DESC';
            }

            // 현재 URL 파라미터 가져오기
            const urlParams = new URLSearchParams(window.location.search);
			
			// 기존 검색 조건 유지
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const ipAddress = document.getElementById('ip_address').value;
            
            if (startDate) urlParams.set('start_date', startDate);
            if (endDate) urlParams.set('end_date', endDate);
            if (ipAddress) urlParams.set('ip_address', ipAddress);
			
            urlParams.set('sort', field);
            urlParams.set('order', newOrder);

            // 페이지 이동
            window.location.href = window.location.pathname + '?' + urlParams.toString();
        });
    });
});


// 검색 유효성 검사 함수
function validateSearch() {
    const searchType = document.getElementById('search_type').value;
    const searchKeyword = document.getElementById('search_keyword').value.trim();
    
    if (!searchType) {
        alert('검색 구분을 선택해주세요.');
        document.getElementById('search_type').focus();
        return false;
    }
    
    if (!searchKeyword) {
        alert('검색어를 입력해주세요.');
        document.getElementById('search_keyword').focus();
        return false;
    }
    
    return true;
}