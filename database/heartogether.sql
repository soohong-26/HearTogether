-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 13, 2025 at 07:08 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `heartogether`
--

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `category`, `question`, `answer`, `category_id`) VALUES
(20, 'Basics of Hearing Impairment', 'What is hearing impairment?', 'Hearing impairment is a partial or total inability to hear, which can affect how people communicate and interact with the world.', NULL),
(21, 'Basics of Hearing Impairment', 'What device can help people with hearing loss hear better?', 'A hearing aid is a small electronic device that helps people with hearing loss hear more clearly by amplifying sound.', NULL),
(22, 'Basics of Hearing Impairment', 'What does a hearing aid do?', 'A hearing aid helps people with hearing loss hear better by making sounds louder and clearer.', NULL),
(23, 'Deaf Culture & Communication', 'What is sign language primarily used for?', 'Sign language is mainly used as a way for people who are deaf or hard of hearing to communicate with others using hand signs and gestures.', NULL),
(24, 'Deaf Culture & Communication', 'What is deaf awareness?', 'Deaf awareness means understanding and respecting the needs, culture, and experiences of people who are deaf or hard of hearing.', NULL),
(25, 'Deaf Culture & Communication', 'What is fingerspelling?', 'Fingerspelling is a part of sign language where each letter of a word is spelled out using hand signs.', NULL),
(26, 'Best Practices for Communicating', 'Why is facial expression important in sign language?', 'Facial expressions show emotions and context, making the meaning of the signs clearer during communication.', NULL),
(27, 'Best Practices for Communicating', 'Which hand can you use in sign language?', 'You can use either hand in sign language, but it\'s important to be consistent and comfortable with your choice.', NULL),
(28, 'Best Practices for Communicating', 'What should you NOT do when someone is lip-reading?', 'Do not cover your mouth, as it makes it difficult for someone to read your lips. Always face the person and speak clearly.', NULL),
(29, 'Environments & Situations', 'What is the best environment for sign language conversations?', 'A well-lit place is best, as it helps people see each other’s hand signs and facial expressions clearly.', NULL),
(30, 'Environments & Situations', 'In a group conversation with deaf friends, what is helpful?', 'It\'s helpful to make sure everyone can see each other, so everyone can follow along and join the conversation.', NULL),
(31, 'Practical Communication Tips', 'What’s one way to get the attention of someone who is deaf?', 'Gently wave your hand or tap the person on the shoulder to get their attention.', NULL),
(32, 'Best Practices for Communicating', 'Which part of the body does hearing mostly involve?', 'Hearing mainly involves the ears.', NULL),
(33, 'Practical Communication Tips', 'Which of the following is a sign language greeting?', 'Waving is a common way to greet someone in sign language.', NULL),
(34, 'General Advice', 'Why is it important to be deaf aware?', 'Being deaf aware means being considerate and respectful, which makes communication and interaction easier and more inclusive for everyone.', NULL),
(35, 'General Advice', 'What should I do if I feel overwhelmed while supporting someone with hearing loss?', 'Feeling overwhelmed is normal when learning how to support someone with hearing loss. Remember to take things step by step, seek support from family, friends, or local organisations, and don’t hesitate to consult with professionals if needed. It’s essential to look after your well-being, too—take breaks, ask questions, and know it’s okay to seek help along the way.', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `faq_categories`
--

CREATE TABLE `faq_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `category_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq_categories`
--

INSERT INTO `faq_categories` (`id`, `name`, `category_order`) VALUES
(79, 'Basics of Hearing Impairment', 0),
(88, 'Deaf Culture & Communication', 0),
(97, 'Best Practices for Communicating', 0),
(112, 'Environments & Situations', 0),
(118, 'Practical Communication Tips', 0),
(127, 'General Advice', 0);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `attempt_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `score` int(11) NOT NULL,
  `attempt_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`attempt_id`, `username`, `score`, `attempt_date`) VALUES
(1, 'PenguinPlays', 0, '2025-05-28 15:02:17'),
(2, 'PenguinPlays', 1, '2025-05-29 20:50:22'),
(3, 'PenguinPlays', 0, '2025-06-01 10:44:16'),
(4, 'PenguinPlays', 2, '2025-06-01 21:03:55'),
(5, 'PenguinPlays', 1, '2025-06-01 21:04:14'),
(6, 'PenguinPlays', 1, '2025-06-12 09:08:05'),
(7, 'Testuser', 5, '2025-06-17 09:46:25'),
(8, 'Testuser', 5, '2025-06-17 09:46:37'),
(9, 'Testuser', 5, '2025-06-17 09:47:12');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `question_id` int(11) NOT NULL,
  `question_text` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `option_a` varchar(100) NOT NULL,
  `option_b` varchar(100) NOT NULL,
  `option_c` varchar(100) NOT NULL,
  `option_d` varchar(100) NOT NULL,
  `correct_option` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`question_id`, `question_text`, `image`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(2, 'What is sign language primarily used for?', NULL, 'Playing games', 'Communicating with the deaf', 'Programming computers', 'Making music', 'B'),
(4, 'What device can help people with hearing loss to hear better?', NULL, 'Smartwatch', 'Calculator', 'Hearing aid', 'Sunglasses', 'C'),
(6, 'Which part of the body does hearing mostly involve?', NULL, 'Fingers', 'Ears', 'Nose', 'Knees', 'B'),
(8, 'Which hand  can you use in sign language?', NULL, 'Only right', 'Only left', 'Both', 'Neither', 'C'),
(9, 'Why is facial expression important in sign language?', NULL, 'For acting', 'It shows emotions and context', 'It\'s not important', 'It scares people', 'B'),
(10, 'What\'s one way to get the attention of someone who is deaf?', NULL, 'Yell loudly', 'Throw something', 'Gently wave or tap', 'Turn off the lights', 'C'),
(11, 'What is fingerspelling?', NULL, 'Spelling with your feet', 'Using hand signs for each letter', 'Typing on a phone', 'Drawing pictures', 'B'),
(12, 'When meeting someone who is Deaf, what is polite to do?', NULL, 'Make eye contact and smile', 'Turn you back when signing', 'Speak very quickly', 'Ignore them', 'A'),
(13, 'What does hearing aid do?', NULL, 'Makes music louder', 'Tells the time', 'Changes the TV channels', 'Helps people with hearing loss hear better', 'D'),
(14, 'What should you NOT do when someone is lip-reading', NULL, 'Speak clearly', 'Face them', 'Cover your mouth', 'Speak at a normal pace', 'C'),
(15, 'Which of the following is a sign language greeting?', NULL, 'Waving', 'Jumping', 'Whispering', 'Blinking', 'A'),
(16, 'In a group conversation with Deaf friends, what is helpful?', NULL, 'Speak behind their backs', 'Make sure everyone can see each other', 'Turn off the lights', 'Use only text messages', 'B'),
(18, 'What is the best environment for sign language conversations?', NULL, 'Dark room', 'Well-lit place', 'Noisy kitchen', 'Busy playground', 'B'),
(24, 'What sign is this?', 'uploads/6850c17bad48b_hello.gif', 'Goodbye', 'Hello', 'Thank you', 'Yes', 'B'),
(25, 'What sign is this?', 'uploads/6850c19c09e7f_thank_you.gif', 'Hello', 'Please', 'Thank you', 'Yes', 'C'),
(26, 'What sign is this?', 'uploads/6850c1bd193f9_how_are_you.gif', 'No', 'Goodbye', 'Yes', 'How are you?', 'D'),
(27, 'What sign is this?', 'uploads/6850c1d6170fb_sorry.gif', 'Thank you', 'Hello', 'Sorry', 'Goodbye', 'C');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_responses`
--

CREATE TABLE `quiz_responses` (
  `response_id` int(11) NOT NULL,
  `attempt_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_option` char(1) NOT NULL,
  `is_correct` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_responses`
--

INSERT INTO `quiz_responses` (`response_id`, `attempt_id`, `question_id`, `selected_option`, `is_correct`) VALUES
(3, 4, 8, 'A', 0),
(4, 4, 4, 'C', 1),
(5, 4, 6, 'B', 1),
(6, 4, 2, 'C', 0),
(7, 4, 10, 'A', 0),
(8, 5, 2, 'B', 1),
(9, 5, 6, 'A', 0),
(10, 5, 4, 'A', 0),
(11, 5, 10, 'A', 0),
(12, 5, 9, 'A', 0),
(14, 6, 18, 'C', 0),
(15, 6, 12, 'A', 1),
(16, 6, 9, 'D', 0),
(17, 6, 4, 'D', 0),
(18, 7, 16, 'B', 1),
(19, 7, 6, 'B', 1),
(20, 7, 8, 'C', 1),
(21, 7, 9, 'B', 1),
(22, 7, 13, 'D', 1),
(23, 8, 16, 'B', 1),
(24, 8, 6, 'B', 1),
(25, 8, 8, 'C', 1),
(26, 8, 9, 'B', 1),
(27, 8, 13, 'D', 1),
(28, 9, 4, 'C', 1),
(29, 9, 24, 'B', 1),
(30, 9, 14, 'C', 1),
(31, 9, 2, 'B', 1),
(32, 9, 18, 'B', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(128) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_img` varchar(255) NOT NULL,
  `roles` varchar(10) NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `profile_img`, `roles`, `is_approved`) VALUES
(1, 'soohong', '$2y$10$7Aw3ek/m3QfZDMxNhflVRuWvSoe3p1.1YZtVKOm.OeDfJCGZ9nZYS', 'TSHplays@gmail.com', 'profile/soohong_683d9be6649b4.png', 'admin', 1),
(2, 'PenguinPlays', '$2y$10$S5tU1WCjMV.Dfjip.mPum.lu04y6srQECAQdlICZc5X9R.kU8.77e', 'soohong@gmail.com', 'profile/PenguinPlays_683d9b78e2b26.jpg', 'user', 1),
(8, 'testing', '$2y$10$NzCkfpSTjsxbv94OEu0RfuCIlWgzs8jTclQ6p90/xnNSmFBjGdTpe', 'testing@gmail.com', 'profile/profile.png', 'user', 0),
(9, 'soohong1', '$2y$10$bdWyA89ZLAv1AxUDuc4cb.qEuHq7QihOrEthD1eHv2MDt2UziHEM2', 'soohongwastaken@gmail.com', 'profile/profile.png', 'user', 1),
(10, '123', '$2y$10$ecFcwpxN7BiMlfFEGLst6e0TXa.8Y.Iak.Mz5c9jtBrS8zNcYv5ya', 'gerg@gmail.com', 'profile/profile.png', 'user', 0),
(11, 'Testuser', '$2y$10$G3DoYMMIL/GJb6.breU5mOoA63O7cbWbev6Jk5RJMlpGHXd6/JicC', 'test@gmail.com', 'profile/profile.png', 'user', 1);

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `video_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`video_id`, `filename`, `title`, `category`) VALUES
(17, 'Z.gif', 'Z', 'Alphabets'),
(18, 'Y.jpg', 'Y', 'Alphabets'),
(19, 'X.jpg', 'X', 'Alphabets'),
(20, 'W.jpg', 'W', 'Alphabets'),
(21, 'V.jpg', 'V', 'Alphabets'),
(22, 'U.jpg', 'U', 'Alphabets'),
(23, 'T.jpg', 'T', 'Alphabets'),
(24, 'S.jpg', 'S', 'Alphabets'),
(26, 'R.jpg', 'R', 'Alphabets'),
(28, 'Q.jpg', 'Q', 'Alphabets'),
(29, 'P.jpg', 'P', 'Alphabets'),
(30, 'O.jpg', 'O', 'Alphabets'),
(31, 'N.jpg', 'N', 'Alphabets'),
(32, 'M.jpg', 'M', 'Alphabets'),
(33, 'L.jpg', 'L', 'Alphabets'),
(34, 'K.jpg', 'K', 'Alphabets'),
(35, 'J.gif', 'J', 'Alphabets'),
(36, 'I.jpg', 'I', 'Alphabets'),
(37, 'H.jpg', 'H', 'Alphabets'),
(40, 'G.jpg', 'G', 'Alphabets'),
(41, 'F.jpg', 'F', 'Alphabets'),
(42, 'E.jpg', 'E', 'Alphabets'),
(43, 'D.jpg', 'D', 'Alphabets'),
(44, 'C.jpg', 'C', 'Alphabets'),
(45, 'B.jpg', 'B', 'Alphabets'),
(46, 'A.jpg', 'A', 'Alphabets'),
(47, 'please_welcome.gif', 'Please (Welcome)', 'Greetings'),
(48, 'congratulations.gif', 'Congratulations', 'Greetings'),
(49, 'thank_you.gif', 'Thank You', 'Greetings'),
(50, 'you are welcome.gif', 'You Are Welcome', 'Greetings'),
(51, 'sorry.gif', 'Sorry', 'Greetings'),
(52, 'excuse.gif', 'Excuse Me', 'Greetings'),
(53, 'fine.gif', 'Fine', 'Greetings'),
(54, 'hello.gif', 'Hello', 'Greetings'),
(55, 'how_are_you.gif', 'How Are You?', 'Greetings'),
(56, 'good_morning.gif', 'Good Morning', 'Greetings'),
(57, 'sweet.gif', 'Sweet', 'Taste'),
(58, 'bitter.gif', 'Bitter', 'Taste'),
(59, 'spicy.gif', 'Spicy', 'Taste'),
(60, 'sour.gif', 'Sour', 'Taste'),
(61, 'ketupat.gif', 'Ketupat', 'Food'),
(62, 'cheese.gif', 'Cheese', 'Food'),
(63, 'cake.gif', 'Cake', 'Food'),
(64, 'meat.gif', 'Meat', 'Food'),
(65, 'chocolate.gif', 'Chocolate', 'Food'),
(66, 'burger.gif', 'Burger', 'Food'),
(67, 'biscuits.gif', 'Biscuits', 'Food');

-- --------------------------------------------------------

--
-- Table structure for table `video_categories`
--

CREATE TABLE `video_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `category_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `video_categories`
--

INSERT INTO `video_categories` (`id`, `name`, `category_order`) VALUES
(19, 'Alphabets', 0),
(186, 'Greetings', 1),
(229, 'Taste', 0),
(243, 'Food', 0);

-- --------------------------------------------------------

--
-- Table structure for table `website_ratings`
--

CREATE TABLE `website_ratings` (
  `rating_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `rated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `website_ratings`
--

INSERT INTO `website_ratings` (`rating_id`, `user_id`, `rating`, `rated_at`) VALUES
(1, 1, 5, '2025-06-13 12:55:17'),
(2, 2, 1, '2025-06-13 12:56:20'),
(3, 9, 5, '2025-06-13 13:49:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faq_categories`
--
ALTER TABLE `faq_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`attempt_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`question_id`);

--
-- Indexes for table `quiz_responses`
--
ALTER TABLE `quiz_responses`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `attempt_id` (`attempt_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`video_id`);

--
-- Indexes for table `video_categories`
--
ALTER TABLE `video_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `website_ratings`
--
ALTER TABLE `website_ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `faq_categories`
--
ALTER TABLE `faq_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `quiz_responses`
--
ALTER TABLE `quiz_responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `video_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `video_categories`
--
ALTER TABLE `video_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=265;

--
-- AUTO_INCREMENT for table `website_ratings`
--
ALTER TABLE `website_ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `quiz_responses`
--
ALTER TABLE `quiz_responses`
  ADD CONSTRAINT `quiz_responses_ibfk_1` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`attempt_id`),
  ADD CONSTRAINT `quiz_responses_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`question_id`);

--
-- Constraints for table `website_ratings`
--
ALTER TABLE `website_ratings`
  ADD CONSTRAINT `website_ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
