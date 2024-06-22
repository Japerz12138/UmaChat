<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UmaChat</title>
    <link href="./resources/css/stylesheet.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">UmaChat</h1>
    <div class="row">
        <div class="col-lg-4">
            <form id="messageForm">
                <label for="name" class="form-label">名称：</label>
                <input type="text" id="nameSearch" class="form-control mb-3" placeholder="搜索名称">
                <select name="name" id="name" class="form-select mb-3">
                    <!-- 名称选项将通过JavaScript动态生成 -->
                </select>
                <label for="message" class="form-label">留言：</label>
                <textarea name="message" id="message" class="form-control mb-3" rows="4" cols="50"></textarea>
                <button type="submit" class="btn btn-primary">提交</button>
            </form>
        </div>
        <div class="col-lg-8">
            <div id="messages" class="row row-cols-1">
                <!-- 留言内容将会显示在这里 -->
            </div>
            <div id="pagination" class="d-flex justify-content-center align-items-center mt-3">
                <!-- 分页控件将会显示在这里 -->
            </div>
        </div>
    </div>
</div>


<!-- 引入Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function loadNames() {
        fetch('get_names.php')
            .then(response => response.json())
            .then(data => {
                const nameSelect = document.getElementById('name');
                nameSelect.innerHTML = '';
                data.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.name;
                    option.textContent = user.name;
                    option.dataset.avatar = user.avatar; // 使用data属性存储头像URL
                    nameSelect.appendChild(option);
                });
            });
    }

    let currentPage = 1;

    function loadMessages(page = 1) {
        fetch(`load_messages.php?page=${page}`)
            .then(response => response.json())
            .then(data => {
                currentPage = page; // 更新当前页码
                const messagesDiv = document.getElementById('messages');
                messagesDiv.innerHTML = '';
                data.messages.forEach(msg => {
                    const card = document.createElement('div');
                    card.classList.add('shadow', 'card', 'mb-3');
                    const cardBody = document.createElement('div');
                    cardBody.classList.add('card-body');
                    const avatarImg = document.createElement('img');
                    avatarImg.src = msg.avatar;
                    avatarImg.classList.add('me-3', 'rounded-circle');
                    avatarImg.style.width = '50px';
                    avatarImg.style.height = '50px';
                    const nameStrong = document.createElement('strong');
                    nameStrong.textContent = msg.name;
                    const messageP = document.createElement('p');
                    messageP.classList.add('card-text');
                    messageP.textContent = msg.message;
                    const timestampSmall = document.createElement('small');
                    timestampSmall.classList.add('text-muted');
                    timestampSmall.textContent = msg.timestamp;
                    cardBody.appendChild(avatarImg);
                    cardBody.appendChild(nameStrong);
                    cardBody.appendChild(document.createElement('br'));
                    cardBody.appendChild(messageP);
                    cardBody.appendChild(timestampSmall);
                    card.appendChild(cardBody);
                    messagesDiv.appendChild(card);
                });

                const totalPages = Math.ceil(data.total_messages / data.messages_per_page);
                const paginationDiv = document.getElementById('pagination');
                paginationDiv.innerHTML = '';

                const prevButton = document.createElement('button');
                prevButton.classList.add('btn', 'btn-secondary', 'me-2');
                prevButton.textContent = '上一页';
                prevButton.disabled = data.current_page === 1;
                prevButton.addEventListener('click', () => loadMessages(data.current_page - 1));
                paginationDiv.appendChild(prevButton);

                const pageIndicator = document.createElement('span');
                pageIndicator.textContent = `第 ${data.current_page} 页，共 ${totalPages} 页`;
                paginationDiv.appendChild(pageIndicator);

                const nextButton = document.createElement('button');
                nextButton.classList.add('btn', 'btn-secondary', 'ms-2');
                nextButton.textContent = '下一页';
                nextButton.disabled = data.current_page === totalPages;
                nextButton.addEventListener('click', () => loadMessages(data.current_page + 1));
                paginationDiv.appendChild(nextButton);

                const pageInput = document.createElement('input');
                pageInput.type = 'number';
                pageInput.classList.add('form-control', 'ms-2');
                pageInput.style.width = '60px';
                pageInput.min = 1;
                pageInput.max = totalPages;
                pageInput.value = data.current_page;
                pageInput.addEventListener('change', (event) => {
                    let newPage = parseInt(event.target.value);
                    if (newPage >= 1 && newPage <= totalPages) {
                        loadMessages(newPage);
                    } else {
                        event.target.value = data.current_page;
                    }
                });
                paginationDiv.appendChild(pageInput);
            });
    }

    document.getElementById('messageForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const nameSelect = document.getElementById('name');
        const selectedOption = nameSelect.options[nameSelect.selectedIndex];
        const avatar = selectedOption.dataset.avatar; // 获取头像URL

        formData.append('avatar', avatar);

        fetch('post_message.php', {
            method: 'POST',
            body: formData
        }).then(response => response.text())
            .then(result => {
                showAlert('发送成功！');
                loadMessages(currentPage); // 重新加载当前页的消息
                event.target.reset();
            })
            .catch(error => console.error('Error posting message:', error));
    });

    document.getElementById('nameSearch').addEventListener('input', function(event) {
        const searchTerm = event.target.value.toLowerCase();
        const nameSelect = document.getElementById('name');
        fetch('get_names.php')
            .then(response => response.json())
            .then(data => {
                nameSelect.innerHTML = '';
                data.forEach(user => {
                    if (user.name.toLowerCase().includes(searchTerm)) {
                        const option = document.createElement('option');
                        option.value = user.name;
                        option.textContent = user.name;
                        option.dataset.avatar = user.avatar;
                        nameSelect.appendChild(option);
                    }
                });
            });
    });

    loadNames(); // 页面加载时加载名称列表
    loadMessages(currentPage); // 页面加载时加载第一页的消息
    setInterval(() => loadMessages(currentPage), 5000); // 每5秒加载一次当前页的留言

</script>

</body>
</html>
