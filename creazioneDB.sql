-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Giu 04, 2021 alle 00:06
-- Versione del server: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `subitoprofessionista`
--
CREATE DATABASE IF NOT EXISTS `subitoprofessionista` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `subitoprofessionista`;

-- --------------------------------------------------------

--
-- Struttura della tabella `intervento`
--

DROP TABLE IF EXISTS `intervento`;
CREATE TABLE IF NOT EXISTS `intervento` (
`id` int(10) unsigned NOT NULL,
  `Professionista` varchar(50) NOT NULL COMMENT 'Email del professionista che ha eseguito l''intervento',
  `TargetUtente` varchar(50) DEFAULT NULL COMMENT 'Email dell''utente che ha ricevuto l''intervento',
  `TargetProfessionista` varchar(50) DEFAULT NULL COMMENT 'Email del professionista che ha ricevuto l''intervento',
  `Data` date NOT NULL,
  `Descrizione` varchar(400) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Svuota la tabella prima dell'inserimento `intervento`
--

TRUNCATE TABLE `intervento`;
--
-- Dump dei dati per la tabella `intervento`
--

INSERT INTO `intervento` (`id`, `Professionista`, `TargetUtente`, `TargetProfessionista`, `Data`, `Descrizione`) VALUES
(18, 'giulio@capecchi.it', 'pippo@pluto.com', NULL, '2020-03-03', 'Collegamenti elettrici impianto di riscaldamento'),
(19, 'giulio@capecchi.it', 'pippo@pluto.com', NULL, '2020-03-04', 'Sostituzione vecchi cablaggi impianto di casa'),
(20, 'giulio@capecchi.it', 'pippo@pluto.com', NULL, '2021-04-02', 'Montaggio nuovi pezzi arrivati in ritardo'),
(21, 'matteo@cesarini.eu', 'pippo@pluto.com', NULL, '2021-04-03', 'Montaggio impianto audio &amp; domotica'),
(22, 'franco.terranova@gmail.com', 'pippo@pluto.com', NULL, '2020-01-02', 'Consulenza privata per la scelta di vini da posizionare in cantina'),
(23, 'franco.terranova@gmail.com', 'pippo@pluto.com', NULL, '2021-02-02', 'Consulenza aziendale'),
(24, 'franco.terranova@gmail.com', NULL, 'giulio@capecchi.it', '2021-05-03', 'Consulenza sul posizionamento di una cantina - Poggibonsi (SI)'),
(25, 'giulio@capecchi.it', 'fede@nardi.it', NULL, '2021-06-01', 'Manutenzione generale di tutto l''impianto elettrico'),
(26, 'giulio@capecchi.it', 'pippo@pluto.com', NULL, '2021-06-03', 'Risoluzione problema relativo alla presa elettrica situata nell''angolo nord-ovest della cucina');

-- --------------------------------------------------------

--
-- Struttura della tabella `professione`
--

DROP TABLE IF EXISTS `professione`;
CREATE TABLE IF NOT EXISTS `professione` (
  `Nome` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Svuota la tabella prima dell'inserimento `professione`
--

TRUNCATE TABLE `professione`;
--
-- Dump dei dati per la tabella `professione`
--

INSERT INTO `professione` (`Nome`) VALUES
('Elettricista'),
('Enologo'),
('Fabbro'),
('Idraulico');

-- --------------------------------------------------------

--
-- Struttura della tabella `professionista`
--

DROP TABLE IF EXISTS `professionista`;
CREATE TABLE IF NOT EXISTS `professionista` (
  `Email` varchar(50) NOT NULL,
  `Password` char(60) NOT NULL,
  `Genere` char(1) NOT NULL,
  `Nome` varchar(20) NOT NULL,
  `Cognome` varchar(15) NOT NULL,
  `Professione` varchar(20) NOT NULL,
  `Prezzi` varchar(130) DEFAULT NULL,
  `Copertura` varchar(1000) NOT NULL,
  `Descrizione` varchar(200) DEFAULT NULL,
  `Contatti` varchar(100) DEFAULT NULL,
  `Orari` varchar(300) DEFAULT NULL,
  `CondivisioneStorico` tinyint(1) NOT NULL COMMENT 'True (=1) se il professionista ha accettato di condividere lo storico degli interventi con tutti gli altri professionisti'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Svuota la tabella prima dell'inserimento `professionista`
--

TRUNCATE TABLE `professionista`;
--
-- Dump dei dati per la tabella `professionista`
--

INSERT INTO `professionista` (`Email`, `Password`, `Genere`, `Nome`, `Cognome`, `Professione`, `Prezzi`, `Copertura`, `Descrizione`, `Contatti`, `Orari`, `CondivisioneStorico`) VALUES
('franco.terranova@gmail.com', '$2y$10$43FUdTJLLaRhQY9cle4Bku.AhznPgtELvlJfTPG.zbdkcxyEqlN/K', 'M', 'Franco', 'Terranova', 'Enologo', 'A partire da &euro;100 per consulenza privata', 'Pisa,Livorno', 'Grande esperto di vini con anni di esperienza', 'Telegram: @francoT\r\nCellulare: 398 399 83 98\r\nEmail: franco.terranova@gmail.com', 'Dal marted&igrave; al gioved&igrave; tutto il giorno', 1),
('giadina@maj.com', '$2y$10$Lc/a6Tm8WQ1wZQsQtJ3BZO6IznTKN8TMymsYxsaxO7iAlrARZLQ2G', 'F', 'Giadina', 'Maj', 'Elettricista', '&euro;500 per impianto di allarme', 'Pisa,Livorno,Siena,Firenze', 'Economica!', 'LinkedIn: @g.maj.8\r\nCellulare: 398 988 38 38', 'Tutto il giorno tutti i giorni!', 1),
('giulio@capecchi.it', '$2y$10$ZewoQybeXezhvRnKLbf3Mekc62kmHymt2DaLdnwK2Dd6H4ksEkA6O', 'M', 'Giulio', 'Capecchi', 'Elettricista', '50 euro per manutenzioni ordinarie', 'Livorno', 'Sempre pronto a tutto!', 'Email: giulio@capecchi.it\r\nCellulare: 366 986 19 85', 'Tutti i giorni dalle 08:00 alle 14:00 e dalle 15:30 alle 19:30', 1),
('lorenzo@pratelli.it', '$2y$10$Hnus5tVTh1yYd5qMnu7TbeJOkzREcsv3TNwgMdg4a9MidDyJ3Ojui', 'M', 'Lorenzo', 'Pratelli', 'Enologo', 'Preventivo in base alle esigenze e alle tempistiche', 'Agrigento', 'Mi piace la musica â™ª', 'Email: lorenzo@pratelli.it', 'Marted&igrave;gioved&igrave;venerd&igrave;: 06:00-13:00 & 16:00-19:00', 1),
('luca.cavallari@yahoo.it', '$2y$10$uDEPaZuX.Ix2PUgR5RG0/OrFixGdfPEypb5B27e6bcbhX3SYcQ3Oi', 'U', 'Luca', 'Cavallari', 'Enologo', 'Preventivo da concordare.\r\nNei festivi si applica una tassa extra.', 'Ferrara', 'Simpatico', 'Whatsapp/Cellulare: +39 311 39 39', 'Variabili', 1),
('marco@bologna.it', '$2y$10$tOFKupc6mO.8BKP192.w9eLEpEsp7G2lxnJfDKuQpGdIYpIYTOxZa', 'U', 'Marco', 'Bologna', 'Elettricista', 'A partire da 50 euro', 'Genova,Imperia,La Spezia,Savona', 'Stacanovista\r\n\r\n30 anni di esperienza alle spalle!', 'Cellulare: marco@bologna.it', 'Dal luned&igrave; al venerd&igrave;: 08:00 - 13:00 & 15:00 - 20:00', 1),
('matteo@cesarini.eu', '$2y$10$YlToYoajQMdLn3O/.ezAQugl5pYZccIaQazTeOCRw/lGr4Mz7K1R6', 'M', 'Matteo', 'Cesarini', 'Elettricista', 'â–ºâ–ºContattarmi per un preventivoâ—„â—„', 'Lucca,Massa-Carrara,Pisa,Pistoia,Prato', 'Socievoleâ™¥', 'Cellullare: 333 245 98 35', 'Tutti i giorni: 10:00-20:00\r\n\r\nEccezionalmente anche dopo cena', 1),
('micheloni.stefano@gmail.com', '$2y$10$pj.pwIe.zwqO7Q42X1SFqeDazmgchtxmvS6yte0KICIp6cfBhX7Ha', 'M', 'Stefano', 'Micheloni', 'Elettricista', 'A partire da 500 euro per impianto completo', 'Massa-Carrara', 'Perfezionista', 'Email: micheloni.stefano@gmail.com\r\nTelegram: @stefanom', 'Tutti i giorni tutto il giorno', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `provincia`
--

DROP TABLE IF EXISTS `provincia`;
CREATE TABLE IF NOT EXISTS `provincia` (
  `Nome` varchar(25) NOT NULL,
  `Regione` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Svuota la tabella prima dell'inserimento `provincia`
--

TRUNCATE TABLE `provincia`;
--
-- Dump dei dati per la tabella `provincia`
--

INSERT INTO `provincia` (`Nome`, `Regione`) VALUES
('Chieti', 'Abruzzo'),
('L''Aquila', 'Abruzzo'),
('Pescara', 'Abruzzo'),
('Teramo', 'Abruzzo'),
('Matera', 'Basilicata'),
('Potenza', 'Basilicata'),
('Catanzaro', 'Calabria'),
('Cosenza', 'Calabria'),
('Crotone', 'Calabria'),
('Reggio Calabria', 'Calabria'),
('Vibo Valentia', 'Calabria'),
('Avellino', 'Campania'),
('Benevento', 'Campania'),
('Caserta', 'Campania'),
('Napoli', 'Campania'),
('Salerno', 'Campania'),
('Bologna', 'Emilia-Romagna'),
('Ferrara', 'Emilia-Romagna'),
('Forli-Cesena', 'Emilia-Romagna'),
('Modena', 'Emilia-Romagna'),
('Parma', 'Emilia-Romagna'),
('Piacenza', 'Emilia-Romagna'),
('Ravenna', 'Emilia-Romagna'),
('Reggio Emilia', 'Emilia-Romagna'),
('Rimini', 'Emilia-Romagna'),
('Gorizia', 'Friuli-Venezia Giulia'),
('Pordenone', 'Friuli-Venezia Giulia'),
('Trieste', 'Friuli-Venezia Giulia'),
('Udine', 'Friuli-Venezia Giulia'),
('Frosinone', 'Lazio'),
('Latina', 'Lazio'),
('Rieti', 'Lazio'),
('Roma', 'Lazio'),
('Viterbo', 'Lazio'),
('Genova', 'Liguria'),
('Imperia', 'Liguria'),
('La Spezia', 'Liguria'),
('Savona', 'Liguria'),
('Bergamo', 'Lombardia'),
('Brescia', 'Lombardia'),
('Como', 'Lombardia'),
('Cremona', 'Lombardia'),
('Lecco', 'Lombardia'),
('Lodi', 'Lombardia'),
('Mantova', 'Lombardia'),
('Milano', 'Lombardia'),
('Monza e Brianza', 'Lombardia'),
('Pavia', 'Lombardia'),
('Sondrio', 'Lombardia'),
('Varese', 'Lombardia'),
('Ancona', 'Marche'),
('Ascoli Piceno', 'Marche'),
('Fermo', 'Marche'),
('Macerata', 'Marche'),
('Pesaro e Urbino', 'Marche'),
('Campobasso', 'Molise'),
('Isernia', 'Molise'),
('Alessandria', 'Piemonte'),
('Asti', 'Piemonte'),
('Biella', 'Piemonte'),
('Cuneo', 'Piemonte'),
('Novara', 'Piemonte'),
('Torino', 'Piemonte'),
('Verbano-Cusio-Ossola', 'Piemonte'),
('Vercelli', 'Piemonte'),
('Bari', 'Puglia'),
('Barletta-Andria-Trani', 'Puglia'),
('Brindisi', 'Puglia'),
('Foggia', 'Puglia'),
('Lecce', 'Puglia'),
('Taranto', 'Puglia'),
('Cagliari', 'Sardegna'),
('Carbonia-iglesias', 'Sardegna'),
('Medio Campidano', 'Sardegna'),
('Nuoro', 'Sardegna'),
('Ogliastra', 'Sardegna'),
('Olbia-Tempio', 'Sardegna'),
('Oristano', 'Sardegna'),
('Sassari', 'Sardegna'),
('Agrigento', 'Sicilia'),
('Caltanissetta', 'Sicilia'),
('Catania', 'Sicilia'),
('Enna', 'Sicilia'),
('Messina', 'Sicilia'),
('Palermo', 'Sicilia'),
('Ragusa', 'Sicilia'),
('Siracusa', 'Sicilia'),
('Trapani', 'Sicilia'),
('Arezzo', 'Toscana'),
('Firenze', 'Toscana'),
('Grosseto', 'Toscana'),
('Livorno', 'Toscana'),
('Lucca', 'Toscana'),
('Massa-Carrara', 'Toscana'),
('Pisa', 'Toscana'),
('Pistoia', 'Toscana'),
('Prato', 'Toscana'),
('Siena', 'Toscana'),
('Bolzano', 'Trentino-Alto Adige'),
('Trento', 'Trentino-Alto Adige'),
('Perugia', 'Umbria'),
('Terni', 'Umbria'),
('Aosta', 'Valle d''Aosta'),
('Belluno', 'Veneto'),
('Padova', 'Veneto'),
('Rovigo', 'Veneto'),
('Treviso', 'Veneto'),
('Venezia', 'Veneto'),
('Verona', 'Veneto'),
('Vicenza', 'Veneto');

-- --------------------------------------------------------

--
-- Struttura della tabella `recensione`
--

DROP TABLE IF EXISTS `recensione`;
CREATE TABLE IF NOT EXISTS `recensione` (
`Id` int(10) unsigned NOT NULL,
  `EmailUtente` varchar(50) DEFAULT NULL COMMENT 'Email dell''utente che ha scritto la recensione',
  `EmailProfessionista` varchar(50) DEFAULT NULL COMMENT 'Email del professionista che ha scritto la recensione',
  `Target` varchar(50) NOT NULL COMMENT 'Email del professionista che è stato recensito',
  `Stelle` tinyint(3) unsigned NOT NULL,
  `Commento` varchar(300) NOT NULL,
  `Data` date NOT NULL COMMENT 'Data di inserimento della recensione'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Svuota la tabella prima dell'inserimento `recensione`
--

TRUNCATE TABLE `recensione`;
--
-- Dump dei dati per la tabella `recensione`
--

INSERT INTO `recensione` (`Id`, `EmailUtente`, `EmailProfessionista`, `Target`, `Stelle`, `Commento`, `Data`) VALUES
(1, 'pippo@pluto.com', NULL, 'giulio@capecchi.it', 5, 'Eccellente lavoro! Strepitoso!', '2021-05-15'),
(2, 'pippo@pluto.com', NULL, 'giulio@capecchi.it', 4, 'Incredibilmente veloce! L''unica pecca &egrave; che non risponde mai al telefono.', '2021-05-17');

-- --------------------------------------------------------

--
-- Struttura della tabella `regione`
--

DROP TABLE IF EXISTS `regione`;
CREATE TABLE IF NOT EXISTS `regione` (
  `Nome` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Svuota la tabella prima dell'inserimento `regione`
--

TRUNCATE TABLE `regione`;
--
-- Dump dei dati per la tabella `regione`
--

INSERT INTO `regione` (`Nome`) VALUES
('Abruzzo'),
('Basilicata'),
('Calabria'),
('Campania'),
('Emilia-Romagna'),
('Friuli-Venezia Giulia'),
('Lazio'),
('Liguria'),
('Lombardia'),
('Marche'),
('Molise'),
('Piemonte'),
('Puglia'),
('Sardegna'),
('Sicilia'),
('Toscana'),
('Trentino-Alto Adige'),
('Umbria'),
('Valle d''Aosta'),
('Veneto');

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

DROP TABLE IF EXISTS `utente`;
CREATE TABLE IF NOT EXISTS `utente` (
  `Email` varchar(50) NOT NULL,
  `Province` varchar(1000) DEFAULT NULL,
  `Password` char(60) NOT NULL,
  `CondivisioneStorico` tinyint(1) NOT NULL COMMENT 'True (=1) se l''utente ha accettato di condividere lo storico degli interventi con tutti i professionisti'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Svuota la tabella prima dell'inserimento `utente`
--

TRUNCATE TABLE `utente`;
--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`Email`, `Province`, `Password`, `CondivisioneStorico`) VALUES
('fede@nardi.it', 'Livorno', '$2y$10$vVblAV.G5Kk02iXyPqV9bOdtF7uPcEUZ/jxt8uSF4FFHNJBfotoS.', 0),
('federicofrati@gmail.com', NULL, '$2y$10$q6816wd2jNh50h6Mgj.Xmuujj9ZHfAMb2iAMkxBrnNDgdK5iwgSa2', 1),
('pippo@pluto.com', NULL, '$2y$10$f.Il1Tj.eMbQZXhmSnDjEOSrNLt3W.BfiPGZAmbi6uMEGn1Scx3We', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `intervento`
--
ALTER TABLE `intervento`
 ADD PRIMARY KEY (`id`), ADD KEY `EmailUtente` (`TargetUtente`,`Professionista`), ADD KEY `EmailProfessionista` (`Professionista`), ADD KEY `EmailProfessionista_2` (`Professionista`), ADD KEY `TargetProfessionista` (`TargetProfessionista`);

--
-- Indexes for table `professione`
--
ALTER TABLE `professione`
 ADD PRIMARY KEY (`Nome`);

--
-- Indexes for table `professionista`
--
ALTER TABLE `professionista`
 ADD PRIMARY KEY (`Email`), ADD KEY `Professione` (`Professione`);

--
-- Indexes for table `provincia`
--
ALTER TABLE `provincia`
 ADD PRIMARY KEY (`Nome`), ADD KEY `Regione` (`Regione`);

--
-- Indexes for table `recensione`
--
ALTER TABLE `recensione`
 ADD PRIMARY KEY (`Id`), ADD KEY `EmailUtente` (`EmailUtente`,`EmailProfessionista`,`Target`), ADD KEY `EmailProfessionista` (`EmailProfessionista`), ADD KEY `Target` (`Target`);

--
-- Indexes for table `regione`
--
ALTER TABLE `regione`
 ADD PRIMARY KEY (`Nome`);

--
-- Indexes for table `utente`
--
ALTER TABLE `utente`
 ADD PRIMARY KEY (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `intervento`
--
ALTER TABLE `intervento`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT for table `recensione`
--
ALTER TABLE `recensione`
MODIFY `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `intervento`
--
ALTER TABLE `intervento`
ADD CONSTRAINT `intervento_ibfk_1` FOREIGN KEY (`Professionista`) REFERENCES `professionista` (`Email`),
ADD CONSTRAINT `intervento_ibfk_2` FOREIGN KEY (`TargetUtente`) REFERENCES `utente` (`Email`),
ADD CONSTRAINT `intervento_ibfk_3` FOREIGN KEY (`TargetProfessionista`) REFERENCES `professionista` (`Email`);

--
-- Limiti per la tabella `professionista`
--
ALTER TABLE `professionista`
ADD CONSTRAINT `professionista_ibfk_1` FOREIGN KEY (`Professione`) REFERENCES `professione` (`Nome`);

--
-- Limiti per la tabella `provincia`
--
ALTER TABLE `provincia`
ADD CONSTRAINT `provincia_ibfk_1` FOREIGN KEY (`Regione`) REFERENCES `regione` (`Nome`);

--
-- Limiti per la tabella `recensione`
--
ALTER TABLE `recensione`
ADD CONSTRAINT `recensione_ibfk_1` FOREIGN KEY (`EmailUtente`) REFERENCES `utente` (`Email`),
ADD CONSTRAINT `recensione_ibfk_2` FOREIGN KEY (`EmailProfessionista`) REFERENCES `professionista` (`Email`),
ADD CONSTRAINT `recensione_ibfk_3` FOREIGN KEY (`Target`) REFERENCES `professionista` (`Email`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
