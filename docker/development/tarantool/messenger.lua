box.cfg{}

-- Создание таблицы
if not box.space.conversation_message then
    box.schema.create_space('conversation_message')

    -- Определение полей таблицы
    box.space.conversation_message:format({
        {name = 'id', type = 'unsigned'},
        {name = 'conversation_id', type = 'unsigned'},
        {name = 'user_id', type = 'unsigned'},
        {name = 'text', type = 'string'},
        {name = 'created_at', type = 'unsigned'},
        {name = 'updated_at', type = 'unsigned', is_nullable = true},
        {name = 'deleted_at', type = 'unsigned', is_nullable = true},
    })

    -- Создание индексов
    box.space.conversation_message:create_index('primary', {
        parts = {'id'},
        type = 'tree',
    })

    box.space.conversation_message:create_index('conversation', {
        parts = {'conversation_id'},
        type = 'tree',
        unique = false,

    })

    box.space.conversation_message:create_index('user', {
        parts = {'user_id'},
        type = 'tree',
        unique = false,
    })

    -- Создание авто инкремента для первичного индекса
    box.schema.sequence.create('CM')

    box.schema.func.create('conversation_messages_select')
    box.schema.user.grant('guest', 'execute', 'function', 'conversation_messages_select')

    box.schema.func.create('conversation_message_insert')
    box.schema.user.grant('guest', 'execute', 'function', 'conversation_message_insert')
end

-- Получение сообщений по идентификатору беседы
function conversation_messages_select(conversation_id, limit, offset)
    return box.space.conversation_message.index.conversation:select(
        {conversation_id},
        {
            iterator = 'LE',
            offset = offset,
            limit = limit,
            order = 'desc',
            sort = 'created_at'
        }
    )
end

-- Добавление нового сообщения
function conversation_message_insert(conversation_id, user_id, message_text, created_at)
    local message_id = box.sequence.CM:next()

    box.space.conversation_message:insert{
        message_id,
        conversation_id,
        user_id,
        message_text,
        created_at
    }

    return message_id
end
