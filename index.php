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
            <div id="messages" class="row row-cols-1 g-3">
                <!-- 留言内容将会显示在这里 -->
            </div>
            <div id="pagination" class="d-flex justify-content-center align-items-center mt-3">
                <!-- 分页内容将会显示在这里 -->
            </div>
        </div>
    </div>
</div>

<!-- 引入Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function createMessageElement(msg, level = 0) {
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
        const replyButton = document.createElement('button');
        replyButton.classList.add('btn', 'btn-link');
        replyButton.textContent = '回复';
        replyButton.addEventListener('click', () => showReplyForm(msg.id, cardBody));

        cardBody.appendChild(avatarImg);
        cardBody.appendChild(nameStrong);
        cardBody.appendChild(document.createElement('br'));
        cardBody.appendChild(messageP);
        cardBody.appendChild(timestampSmall);
        cardBody.appendChild(replyButton);
        card.appendChild(cardBody);

        if (msg.replies && msg.replies.length > 0) {
            const repliesDiv = document.createElement('div');
            repliesDiv.classList.add('ms-4');

            msg.replies.forEach(reply => {
                repliesDiv.appendChild(createMessageElement(reply, level + 1));
            });

            if (level >= 3) {
                const collapseButton = document.createElement('button');
                collapseButton.classList.add('btn', 'btn-link');
                collapseButton.textContent = '显示更多回复';
                collapseButton.addEventListener('click', () => {
                    repliesDiv.style.display = repliesDiv.style.display === 'none' ? 'block' : 'none';
                    collapseButton.textContent = repliesDiv.style.display === 'none' ? '显示更多回复' : '隐藏回复';
                });
                repliesDiv.style.display = 'none';
                cardBody.appendChild(collapseButton);
            }

            card.appendChild(repliesDiv);
        }

        return card;
    }

    function loadMessages(page = 1) {
        fetch(`load_messages.php?page=${page}`)
            .then(response => response.json())
            .then(data => {
                currentPage = page; // 更新当前页码
                const messagesDiv = document.getElementById('messages');
                messagesDiv.innerHTML = '';
                data.messages.forEach(msg => {
                    messagesDiv.appendChild(createMessageElement(msg));
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

                const goButton = document.createElement('button');
                goButton.classList.add('btn', 'btn-secondary', 'ms-2');
                goButton.textContent = '跳转';
                goButton.addEventListener('click', () => {
                    let newPage = parseInt(pageInput.value);
                    if (newPage >= 1 && newPage <= totalPages) {
                        loadMessages(newPage);
                    } else {
                        pageInput.value = data.current_page;
                    }
                });
                paginationDiv.appendChild(goButton);
            })
            .catch(error => console.error('Error loading messages:', error));
    }

    function showReplyForm(parentId, parentElement) {
        const replyForm = document.createElement('form');
        replyForm.classList.add('mt-3');
        const nameInput = document.createElement('input');
        nameInput.type = 'text';
        nameInput.name = 'name';
        nameInput.placeholder = '名称';
        nameInput.classList.add('form-control', 'mb-2');
        const messageInput = document.createElement('textarea');
        messageInput.name = 'message';
        messageInput.placeholder = '回复内容';
        messageInput.classList.add('form-control', 'mb-2');
        const submitButton = document.createElement('button');
        submitButton.type = 'submit';
        submitButton.classList.add('btn', 'btn-primary');
        submitButton.textContent = '提交';
        replyForm.appendChild(nameInput);
        replyForm.appendChild(messageInput);
        replyForm.appendChild(submitButton);

        replyForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(replyForm);
            const avatar = document.querySelector('#name option[selected]').dataset.avatar;
            formData.append('avatar', avatar);
            formData.append('parent_id', parentId);

            fetch('post_message.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(result => {
                    alert('回复成功！');
                    loadMessages(currentPage);
                })
                .catch(error => console.error('Error posting reply:', error));
        });

        parentElement.appendChild(replyForm);
    }

    let currentPage = 1;
    loadMessages();

    setInterval(() => loadMessages(currentPage), 5000);

    document.getElementById('messageForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const nameSelect = document.getElementById('name');
        const selectedOption = nameSelect.options[nameSelect.selectedIndex];
        const avatar = selectedOption.dataset.avatar;
        formData.append('avatar', avatar);

        fetch('post_message.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then(result => {
                alert('发送成功！');
                loadMessages(currentPage);
                event.target.reset();
            })
            .catch(error => console.error('Error posting message:', error));
    });

    document.getElementById('nameSearch').addEventListener('input', function(event) {
        const searchTerm = event.target.value.toLowerCase();
        const nameSelect = document.getElementById('name');
        const options = Array.from(nameSelect.options);
        const filteredOptions = options.filter(option => option.value.toLowerCase().includes(searchTerm));
        nameSelect.innerHTML = '';
        filteredOptions.forEach(option => {
            const newOption = document.createElement('option');
            newOption.value = option.value;
            newOption.textContent = option.value;
            newOption.dataset.avatar = option.dataset.avatar;
            nameSelect.appendChild(newOption);
        });
    });

    function loadNames() {
        fetch('load_names.php')
            .then(response => response.json())
            .then(data => {
                const nameSelect = document.getElementById('name');
                nameSelect.innerHTML = '';
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.name;
                    option.textContent = item.name;
                    option.dataset.avatar = item.avatar;
                    nameSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading names:', error));
    }

    loadNames();
</script>
</body>
</html>
